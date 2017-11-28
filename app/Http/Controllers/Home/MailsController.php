<?php


namespace App\Http\Controllers\Home;

use App\Models\PasswordStaff;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use App\Models\Company;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MailsController extends HomeAppController
{
    /**
     * @param Request $request
     * @return $this
     */
    public function putConfirmEmail(Request $request)
    {
        $token = $request['rid'];
        $company_name = $request['cp_n'];
        $rem = $request['rem'];
        $user = User::where('register_token', $token)->first();
//        pr($user);die;

        if (!empty($user) && (strtotime($user->register_token_expire) >= time())) {
            User::where('register_token', $token)->update(['status' => 3, 'register_token' => null]);

            return view('home.mails.goToApp')->with(['flag' => 'register', 'rem' => $rem, 'cp_n' => $company_name, 'mess' => __('Register Successfull')]);
        } else {
            return view('home.mails.goToApp')->with(['flag' => '', 'rem' => '', 'cp_n' => $company_name, 'mess' => __('Register expired')]);
        }
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function putConfirmEmailChangeProfile(Request $request)
    {
        $token = $request['rid'];
        $company_name = $request['cp_n'];
        $rem = $request['rem'];
        $dataSave['change_email_token'] = null;
        $dataSave['email'] = $rem;
        $user = User::where('change_email_token', $token)->first();

        if (!empty($user) && (strtotime($user->change_email_token_expire) >= time())) {
            $emailUsed = json_decode($user->email_used, true);
            $emailUsed[] = $rem;
            $dataSave['email_used'] = json_encode($emailUsed);

            $user->fill($dataSave);
            $user->save();
            User::where('change_email_token', $token)->update(['status' => 3, 'register_token' => null]);

            return view('home.mails.goToApp')->with(['flag' => 'changeEmail', 'rem' => $rem, 'cp_n' => $company_name, 'mess' => __('Change email Successfull')]);
        } else {
            return view('home.mails.goToApp')->with(['flag' => '', 'rem' => '', 'cp_n' => $company_name, 'mess' => __('Change email expired')]);
        }
    }


    /**
     * @param $token
     * @return $this|array
     */
    public function putResetPassword(Request $request)
    {
        $token = $request['rid'];
        $company_name = $request['cp_n'];
        $user = User::where('reset_pass_token', $token)->first();

        if (!empty($user) && (strtotime($user->reset_pass_token_expire) >= time())) {
            User::where('reset_pass_token', $token)->update(['status' => 3]);
            return view('home.mails.resetpass')->with($user->toArray());
        } else {
            return view('home.mails.goToApp')->with(['flag' => '', 'rem' => '', 'cp_n' => $company_name, 'mess' => __('Reset password expired')]);
        }
    }

    /**
     * @param Request $request
     * @return View
     */
    public function postUpdatePassword(Request $request)
    {
        $user = User::where('id', $request['id'])->where('status', User::STATUS_MEMBERS)->first();
        if (empty($user)) {
            return view('errors.404');
        }
        $compan_name = Company::where('id', $user->company_id)->first()->name;
        $validator = Validator::make($request->all(), [
            "password" => "required|min:6|alpha_num|regex:'[A-Za-z]+'",
        ]);

        if ($validator->fails()) {
            return redirect('mails/reset-password?rid=' . $user->reset_pass_token)->with('errors', $validator->errors())->withInput();
        }

        $passwordStaff = PasswordStaff::where('staff_id', $user->id)->where('type', PasswordStaff::ACC_TYPE_USER)->where('password', $request['password'])->first();
        if (empty($passwordStaff)) {
            PasswordStaff::create(['staff_id' => $user->id, 'type' => PasswordStaff::ACC_TYPE_USER, 'times' => 1, 'last_change_password' => date('Y-m-d H:i:s'), 'password' => $request['password']]);
        } else {
            if ($passwordStaff->times >= 5) {
                return redirect('mails/reset-password?rid=' . $user->reset_pass_token)->with('errs', __('Password was used too many times allowed'));
            } else {
                $passwordStaff->times = $passwordStaff->times + 1;
                $passwordStaff->save();
            }
        }

        $passHash = Hash::make($request['password']);
//        $resetPassCount = $user->reset_pass_count - 1;
        User::where('id', $request['id'])->update(['password' => $passHash, 'reset_pass_token' => null]);
        return view('home.mails.goToApp')->with(['flag' => 'resetpass', 'rem' => '', 'cp_n' => $compan_name, 'mess' => __('Reset password complete.')]);
    }

    public function getOpenApp(Request $request)
    {

        return view('home.mails.goToApp')->with(['flag' => $request['flag'], 'namest' => $request['namest'], 'rid' => $request['rid'], 'patientReplySetting' => $request['patientReplySetting'], 'cp_n' => $request['cp_n'], 'mess' => '']);
    }


}