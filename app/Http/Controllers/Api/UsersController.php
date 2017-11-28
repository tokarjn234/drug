<?php

namespace App\Http\Controllers\Api;


use App\Models\Order;
use App\Models\PasswordStaff;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UsersController extends ApiAppController
{

    /**
     * Registers new user
     * @uri /api/users/register
     * @method POST
     * @param Request $request
     * @return array
     */
    public function postRegister(Request $request)
    {
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');
        $defaultValidate = [
            'email' => 'email|max:255',
            'password' => 'required|min:6',
            'gender' => 'in:0,1,2',
            'birthday' => 'date_format:Y-m-d',
        ];
        $systemST = $this->getRegisterSetting($request);
        $systemST = $systemST['data'];
        unset($systemST['drugbook_use']);
        unset($systemST['drugbrand_change']);

        foreach ($systemST as $key => $value) {
            if ($key == 'first_name' && $value['required'] == true) {
                $validate['last_name'] = 'required';
            }
            if ($key == 'first_name_kana' && $value['required'] == true) {
                $validate['last_name_kana'] = 'required';
            }
            if ($value['required'] == true) {
                $defaultValidate[$key] = isset($defaultValidate[$key]) ? $defaultValidate[$key] . '|required' : 'required';
            }
        }

        $validator = Validator::make($request->all(), $defaultValidate);

        if ($validator->fails()) {
            return r_err($validator->errors());
        }

        $data = $request->all();

        if (isset($request['order_alias'])) {
            unset($data['order_alias']);
        }

        $data['company_id'] = Company::findByAlias($request->headers->get('company'), 'id');
        $data['password'] = Hash::make($data['password']);
        $data['status'] = User::STATUS_TEMPORARY_MEMBERS;
//        $data['reset_pass_count'] = Setting::getResetPassCount($data['company_id']);
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['email_used'] = json_encode([$request['email']]);

        $data['register_token'] = str_random(64);
        $data['register_token_expire'] = date('Y-m-d H:i:s', strtotime('+1 days', time()));
        $data['mail_reminder_time'] = date('Y-m-d H:i:s', strtotime('+1 days', time()));
        unset($data['grant_type']);

        $issetUser = User::where('email_index', md5($request['email']))
            ->where('company_id', $companyId)
            ->where('status', User::STATUS_MEMBERS)
            ->first();
        $userNotActiveExpired = User::where('email_index', md5($request['email']))
            ->where('company_id', $companyId)
            ->where('status', User::STATUS_TEMPORARY_MEMBERS)
            ->where('register_token_expire', '<', date('Y-m-d H:i:s', time()))
            ->first();
        $userNotActive = User::where('email_index', md5($request['email']))
            ->where('company_id', $companyId)
            ->where('status', User::STATUS_TEMPORARY_MEMBERS)
            ->where('register_token_expire', '>=', date('Y-m-d H:i:s', time()))
            ->first();

        if (!empty($issetUser)) {
            return r_err(['email' => 'Email already exists.'], __('Email already exists.'));
        }

        DB::beginTransaction();
        if (!empty($userNotActiveExpired)) {
            User::where('id', $userNotActiveExpired->id)->update($data);
            $newUser = $userNotActiveExpired;
        } else {
            if (!empty($userNotActive)) {
                return r_err(['email' => 'Email not actived'], __('Email not actived'));
            }
            $newUser = User::create($data);
            $newUser->save();
        }

        $userId = $newUser->id;

        if (!empty($request['order_alias'])) {
            $idOrder = Order::findByAlias($request['order_alias'], 'id');
            if (!empty($idOrder)) {
                Order::where('id', $idOrder)->update(['user_id' => $userId]);
            }
        }

        $user = $newUser->toArray();

        $user['company_name'] = Company::findByAlias($request->headers->get('company'), 'name');
        $user['email_register'] = $data['email'];

//        Send email
        Mail::send('email.welcome', $user, function ($message) use ($user) {
            $message->from(env('MAIL_USERNAME'), 'スマホ処方めーる');
            $message->to($user['email_register'], 'Hello user')->subject('【スマホ処方めーる/' . $user['company_name'] . 'ドラッグ】会員登録');
        });

        if (Mail::failures()) {
            DB::rollBack();
        }

        DB::commit();

        return r_ok([$data], __('Register success! Please go to mail and active account'));
    }


    /**
     * Changes password
     * @uri /api/users/change-password
     * @method POST
     * @param Request $request
     * @return array
     */
    public function postChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:6',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return r_err($validator->errors());
        }

        $checkPasswordUser = User::where('alias', $request->token['user_alias'])->first();

        if (!empty($checkPasswordUser) && Hash::check($request['current_password'], $checkPasswordUser->password)) {
            $newPassHash = Hash::make($request['new_password']);
            $result = User::where('id', $checkPasswordUser->id)->update(array('password' => $newPassHash));

            return ($result > 0) ? r_ok([$request->all()], 'Change password successfully') : r_err([$request->all()], 'Change password failed');
        } else {

            return r_err('Invalid current password');
        }
    }

    /**
     * Change profile
     * @uri /api/users/change-profile
     * @method POST
     * @param Request $request
     * @return array
     */
    public function postChangeProfile(Request $request)
    {
        if ($request->token['user_id'] == 'anonymous') {
            return r_err(['You are anonymous'], ['You are anonymous']);
        }

        $user = User::where('alias', $request->token['user_alias'])->first();
//        var_dump($user->toArray());die;
        $issetEmail = null;
        if (!empty($request['email'])) {
            $issetEmail = User::where('email_index', md5($request['email']))
                ->where('company_id', $user->company_id)
                ->where('alias', '<>', $user->alias)
//                ->whereIn('status',[User::STATUS_MEMBERS, User::STATUS_TEMPORARY_MEMBERS, ])
                ->first();
        }

        $defaultValidate = array(
            'gender' => 'in:0,1,2',
            'birthday' => 'date_format:Y-m-d',
            'password' => 'min:6',
            'email' => 'email|max:255'
        );

        $systemST = $this->getRegisterSetting($request);
        $systemST = $systemST['data'];
        unset($systemST['drugbook_use']);
        unset($systemST['drugbrand_change']);

        foreach ($systemST as $key => $value) {
            if ($key == 'first_name' && $value['required'] == true) {
                $validate['last_name'] = 'required';
            }
            if ($key == 'first_name_kana' && $value['required'] == true) {
                $validate['last_name_kana'] = 'required';
            }
            if ($value['required'] == true) {
                $defaultValidate[$key] = isset($defaultValidate[$key]) ? $defaultValidate[$key] . '|required' : 'required';
            }
        }
        if (!empty($user->email_used && md5($request['email']) != $user->email_index)) {
            $emailUsed = json_decode($user->email_used, true);
            if (in_array($request['email'], $emailUsed)) {
                $issetEmail = true;
            }
        }
        if (!empty($issetEmail)) {
            return r_err([], __('Email already exists.'), 1);
        }
        $validator = Validator::make($request->all(), $defaultValidate);

        if ($validator->fails()) {
            return r_err($validator->errors());
        }

        $data = $request->all();

        $dataEmail = [];
        if (!Hash::check($request['password'], $user->password)) {
            $passwordStaff = PasswordStaff::where('staff_id', $user->id)->where('type', PasswordStaff::ACC_TYPE_USER)->where('password', $request['password'])->first();
            if (empty($passwordStaff)) {
                PasswordStaff::create(['staff_id' => $user->id, 'type' => PasswordStaff::ACC_TYPE_USER, 'times' => 1, 'last_change_password' => date('Y-m-d H:i:s'), 'password' => $request['password']]);
            } else {
                if ($passwordStaff->times >= 5) {
                    return r_ok([], __('Password was used too many times allowed'), 3);
                } else {
                    $passwordStaff->times = $passwordStaff->times + 1;
                    $passwordStaff->save();
                }
            }

        }

        if (!empty($request['password'])) {
            $user->password = Hash::make($request['password']);
        }

        if (empty($request['email'])) {
            unset($data['email']);
        }

        $dataEmail['change_email_token'] = str_random(64);

        $dataEmail['change_email_token_expire'] = date('Y-m-d H:i:s', strtotime('+6 hours', time()));
        User::where('id', $user->id)->update($dataEmail);

        if (!empty($request['email']) && $user->email_index != md5($request['email'])) {
            $dataEmail['new_email'] = $request['email'];
            $dataEmail['company_name'] = 'Comp1';
            Mail::send('email.changeEmail', $dataEmail, function ($message) use ($dataEmail) {
                $message->from(env('MAIL_USERNAME'), 'スマホ処方めーる');
                $message->to($dataEmail['new_email'], 'Hello user')->subject('【スマホ処方めーる/' . $dataEmail['company_name'] . 'ドラッグ】パスワードの再設定');
            });
        }

        unset($data['grant_type']);
        unset($data['email']);
        unset($data['password']);

        $user->fill($data);
        $result = $user->save();

        $userAfterSave = User::where('id', $user->id)->first()->toArray();

        return $result ? r_ok($userAfterSave, __('Change Profile success')) : r_err(['Change profile failed'], 'Change profile failed');
    }

    /**
     * Lock Account
     * @uri /api/users/lock-account
     * @method POST
     * @param Request $request
     * @return array
     */

    public function postUnRegister(Request $request)
    {
        if ($request->token['user_id'] == 'anonymous') {
            return r_err(['You are anonymous'], __('You are anonymous'));
        }

        $user = User::where('alias', $request->token['user_alias'])->first();
        $userId = $user->id;

        $order = Order::where('user_id', $userId)->orderBy('created_at', 'asc')->first();

        if (!empty($order)) {
            $firstOrder = Order::where('user_id', $userId)->orderBy('created_at', 'asc')->first()->created_at;
            $lastOrder = Order::where('user_id', $userId)->orderBy('created_at', 'desc')->first()->created_at;
            $dateAfter = strtotime('+2 Years', strtotime($firstOrder));
            $saveFile = strtotime($lastOrder) >= $dateAfter ? true : false;
            if ($saveFile) {
                $listOrders = Order::where('user_id', $userId)->orderBy('created_at', 'asc')->get()->toArray();
//            Save data into file
//            Delete all orders
            }
        }


        $result = DB::table('users')->where('id', $userId)
            ->update(array(
                'status' => User::STATUS_EXITED, 'deleted_at' => date('Y-m-d H:i:s', time()),
                'first_name' => null, 'last_name' => null, 'first_name_kana' => null, 'last_name_kana' => null,
                'email' => null, 'email_index' => null, 'full_name_index' => null, 'full_name_kana_index' => null,
                'phone_number' => null,
                'password' => null,
                'exited_at' => date('Y-m-d H:i:s', time()),
            ));

        return ($result > 0) ? r_ok([], __('Successfully')) : r_err(['Can not lock the account'], 'Can not lock the account');
    }

    /**
     * Get user info
     * @uri /api/users/user-info
     * @method GET
     * @param Request $request
     * @return array
     */

    public function getUserInfo(Request $request)
    {
        if ($request->token['user_id'] == 'anonymous') {
            return r_ok([], ['You are anonymous']);
        } else {
            $user = User::where('alias', $request->token['user_alias'])->first();
            return r_ok($user, 'Successfully');
        }

    }

    /**
     * Reset password
     * @uri /api/users/reset-password
     * @method GET
     * @param Request $request
     * @return array
     */

    public function postResetPassword(Request $request)
    {
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');
        $email = $request['email'];
        $userNotActive = User::where('email_index', md5($email))
            ->where('company_id', $companyId)
            ->where('status', User::STATUS_TEMPORARY_MEMBERS)
            ->first();

        if (!empty($userNotActive)) {
            return r_ok([], __('Account not been actived'), 1);
        }

        $user = User::where('email_index', md5($email))
            ->where('company_id', $companyId)
            ->where('status', User::STATUS_MEMBERS)
            ->first();

        if (empty($user)) {
            return r_ok([], __('Account does not exits'), 2);
        }


        $strToken = str_random(64);

        User::where('id', $user->id)->update(['reset_pass_token' => $strToken, 'reset_pass_token_expire' => date('Y-m-d H:i:s', strtotime('+6 hours', time()))]);
        $user = User::where('id', $user->id)->first()->toArray();
        $user['company_name'] = Company::findByAlias($request->headers->get('company'), 'name');
        $user['email_login'] = $email;
        Mail::send('email.resetpass', $user, function ($message) use ($user) {
            $message->from(env('MAIL_USERNAME'), 'スマホ処方めーる');
            $message->to($user['email_login'], 'Hello user')->subject('【スマホ処方めーる/' . $user['company_name'] . 'ドラッグ】パスワードの再設定');
        });

        return r_ok([], __('Successfull. Please check email to change password'));

    }

    /**
     * Link to order when user register
     * @uri /api/users/user-with-order
     * @method POST
     * @param Request $request
     * @return array
     */
    public function postUserWithOrder(Request $request)
    {
        $userId = User::findByAlias($request['user_alias'], 'id');
        if (empty($userId)) {
            return r_err(['User not found'], 'User not found');
        }
        $result = Order::where('alias', $request['order_alias'])->update(['user_id' => $userId]);
        return ($result > 0) ? r_ok([], 'Successfully') : r_err(['Err'], 'Not update');
    }

    /**
     *
     * @param Request $request
     * @return array
     */
    public function getHello(Request $request)
    {
        return r_ok($request->token['user_alias'], 'Hello message from DrugOrder system');
    }

    private static function getRegisterSetting(Request $request)
    {
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');
//
//        $registerFields = [
//            'first_name' => ['display' => true, 'required' => true],
//            'first_name_kana' => ['display' => true, 'required' => true],
//            'gender' => ['display' => false, 'required' => false],
//            'birthday' => ['display' => true, 'required' => false],
//            'phone_number' => ['display' => true, 'required' => true],
//            'email' => ['display' => true, 'required' => true],
//            'postal_code' => ['display' => true, 'required' => true],
//            'address' => ['display' => true, 'required' => true],
//            'accept_saleinfo' => ['display' => true, 'required' => false],
//            'accept_saleinfo_dm' => ['display' => true, 'required' => false],
//        ];
        $registerSettings = Setting::whereCompanyId($companyId)->whereName('CompanyRegisterSetting')->where('key', 'user_input')->get()->lists('value', 'key')->toArray();

        if (empty ($registerSettings)) {
            $userRegisterSetting = User::$defaultRegisterSetting;
        } else {
            $userRegisterSetting = json_decode($registerSettings['user_input'], true);
        }


        return r_ok($userRegisterSetting, 'Success');
    }


}