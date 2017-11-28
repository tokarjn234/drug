<?php

namespace App\Http\Controllers\Api;

use App\Models\Device;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use DB;
use Hash;


class OAuthTokensController extends ApiAppController
{
    /***
     * @uri /api/oauth/getAccessToken
     * @method POST
     * @input
     * grant_type = 'password'
     * username = string
     * password = string
     * @return JsonResponse
     */
    public function getAccessToken(Request $request)
    {

        $oauth = \App\Lib\OAuth2Helper::initOauthServer();

        $user = (object)['email' => 'anonymous', 'password' => 'anonymous', 'alias' => ''];
        $issetUser = false;

        $companyStatus = Company::findByAlias($request->headers->get('company'), 'status');
        if ($companyStatus == Company::STATUS_CANCELLATION_COMPLETED) {
            return r_err(['Company is not active'], __('Company is not active'), 4);
        }

        if (($request['username'] || $request['email']) && $request['username'] != 'anonymous') {
            $email = $request['username'];

            if (empty ($email)) {
                $email = $request['email'];
            }

            $companyId = Company::findByAlias($request->headers->get('company'), 'id');

            $userNotActive = User::whereCompanyId($companyId)->whereStatus(User::STATUS_TEMPORARY_MEMBERS)->whereEmailIndex(md5($email))->first();
            if (!empty($userNotActive)) {
                return r_err([__('Email not actived')], __('Email not actived'));
            }

            $user = User::whereCompanyId($companyId)->whereStatus(User::STATUS_MEMBERS)->whereEmailIndex(md5($email))->first();


            if (empty ($user)) {
                return r_err([__('Invalid email or password.')], __('Invalid email or password.'));
            }

            $issetUser = true;

            $isValidPassword = Hash::check($request['password'], $user->password);

            if (!$isValidPassword) {
                return r_err([__('Invalid email or password.')], __('Invalid email or password.'));
            }
        }


        $users = [$user->email => ['username' => $user->email, 'password' => $user->password]];

        // create a storage object
        $storage = new \OAuth2\Storage\Memory(array('user_credentials' => $users));

        // create the grant type
        $grantType = new \OAuth2\GrantType\UserCredentials($storage);

        // add the grant type to your OAuth server
        $oauth->addGrantType($grantType);
        $_post = $_POST;
        $_post['password'] = $user->password;
        $_post['username'] = $user->email;

        $oauthRequest = new \OAuth2\Request($_GET, $_post, array(), $_COOKIE, $_FILES, $_SERVER);

        $responseBody = $oauth->handleTokenRequest($oauthRequest)->getResponseBody();

        $response = json_decode($responseBody, true);

        if (isset ($response['access_token'])) {
            // generate app key
            $response['app_keycode'] = str_random(256);

            unset($response['token_type'], $response['scope'], $response['refresh_token']);

            if (config('app.debug')) {
                $response['debug_hashed_keycode'] = sha1($response['app_keycode']);
            }
            //Create User anonymous
            $deviceDB = Device::where('device_code', $request['device_code'])->first();

            if (empty($deviceDB)) {
                if (!$issetUser) {
                    $device['user_id'] = User::insertGetId(['email' => 'anonymous', 'company_id' => Company::findByAlias($request->headers->get('company'), 'id')]);
                } else {
                    $device['user_id'] = $user->id;
                }
                $device['device_type'] = $request->headers->get('php-auth-user');
                $device['device_code'] = $request['device_code'];
                $device['name'] = $request['name'];
                $device['platform'] = $request['platform'];
                $device['version'] = $request['version'];
                $device['status'] = 0;
                $device['created_at'] = date('Y-m-d H:i:s', time());
                $deviceId = Device::insertGetId($device);
            } else {
                $deviceId = $deviceDB->id;
                if ($issetUser) {
                    Device::where('id', $deviceDB->id)->update(['user_id' => $user->id, 'status' => 1]);
                } else {
                    Device::where('id', $deviceDB->id)->update(['status' => 0]);
                }
            }


            $response['user'] = $user;

            if (DB::update('UPDATE `oauth_access_tokens` SET `app_keycode`=?, `device_id`=?, `user_alias`=? WHERE `access_token`=?', [$response['app_keycode'], $deviceId, $user->alias, $response['access_token']]) > 0) {
                return r_ok($response, 'Logged in as ' . $user->email);
            } else {
                return r_err(['Can not create app_keycode']);
            }

        }

        return r_err([$response]);

    }

    public function postLogout(Request $request)
    {
        $notifiToken = $request['notifi_token'];
        $deviceId = $request->token['device_id'];
//        $accessToken = $request->token['access_token'];
        $dataSave = [
            'notification_token' => '',
            'status' => 0
        ];
        $result = Device::where('notification_token', $notifiToken)->where('id', '<>', $deviceId)->update($dataSave);
        return r_ok([], 'Successfully');

    }

    public function getTerm()
    {
        return view('term.term');
    }

    public function getHelp()
    {
        return view('term.help');
    }
}