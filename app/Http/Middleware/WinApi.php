<?php

namespace App\Http\Middleware;

use App\Models\AccessTokenWinApp;
use Closure;
use App\Models\DebugLog;
use Mockery\Exception;
use Illuminate\Contracts\Auth\Guard;

class WinApi
{
    public function handle($request, Closure $next)
    {
        $access = getallheaders();
//        var_dump($access);die;
        if (!isset($access['access_token'])) {
            return r_winapp([], ['access_token not exits'], 1);
        }
        $accessToken = $access['access_token'];
        $user = AccessTokenWinApp::where('access_token', $accessToken)->first();
//        var_dump($user);die;
        if (empty($user)) {
            return r_winapp(['Invalid access token'], ['Invalid access token'], 1);
        }
        $request->access_token = $accessToken;
//        var_dump($user);die;
        $response = $next($request);

        try {
//            $entry = DebugLog::init();
//            $entry->message = 'ApiDebug';
//            $entry->event_type = 'info';
//
//            if (!empty ($request->token)) {
//                $entry->user_id = $request->token['user_id'];
//            }
//
//            $entry->response = json_encode($response->original);
//            $entry->save();
        } catch (\Exception $e) {

        }
        return $response;

    }
}
