<?php

namespace App\Http\Controllers\Company;

use App\Models\AccessTokenWinApp;
use App\Models\Certificate;
use App\Models\PasswordStaff;
use App\Models\Staff;
use App\Models\User;
use App\Models\Company;
use App\Models\Store;
use Illuminate\Http\Request;
use Auth;
use DateTime;

use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends CompanyAppController
{
    /**
     * @param Request $request
     * @throws
     * @return View
     */
    public function login(Request $request)
    {

        if (!is_https()) {
            return redirect()->to(secure_url(action('Company\AuthController@login')));
        }

        if (Auth::user()) {
            return redirect()->action('Company\CompaniesController@getIndex');
        }

        if ($request->isMethod('post')) {

            $loginData = array(
                'username' => $request->input('username'),
                'password' => $request->input('password'),
                'company_id' => $request['company_id']
            );
//            pr($loginData);die;

            if (Auth::attempt($loginData)) {

                $staff = Auth::user();

                $company = Company::find($staff->company_id);

                if (empty ($company)) {
                    Auth::logout();
                    \Session::flush();
                    throw new \Exception('Company record not found!');
                }

                //check company
                if (!empty($company)) {
                    if ($company->status == Company::STATUS_CANCELLATION_COMPLETED) {
                        Auth::logout();
                        \Session::flush();

                        return redirect()->action('Company\AuthController@login')
                            ->withErrors(['LoginFailed' => __('The company suspend operations')])
                            ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);
                    }
                }


                if ($staff->account_type !== Staff::ACCOUNT_TYPE_COMPANY) {
                    Auth::logout();
                    \Session::flush();
                    return redirect()->action('Company\AuthController@login')
                        ->withErrors(['LoginFailed' => __('InvalidUsernameOrPassword')])
                        ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);
                }

                if ($staff->account_type == Staff::ACCOUNT_TYPE_COMPANY && $staff->status == Staff::STATUS_DELETED) {
                    Auth::logout();
                    \Session::flush();
                    return redirect()->action('Company\AuthController@login')->withErrors(['LoginFailed' => __('AccountDeleted')])->withInput();
                }

                if ($staff->account_type == Staff::ACCOUNT_TYPE_COMPANY && $staff->status == Staff::STATUS_ACCOUNT_LOCK) {
                    Auth::logout();
                    \Session::flush();
                    return redirect()->action('Company\AuthController@login')
                        ->withErrors(['LoginFailed' => __('AccountDeleted')])->withInput()
                        ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);
                }

                if ($staff->account_type == Staff::ACCOUNT_TYPE_COMPANY && $staff->status == Staff::STATUS_LOCKOUT) {
                    Auth::logout();
                    \Session::flush();
                    return redirect()->action('Company\AuthController@login')
                        ->withErrors(['LoginFailed' => __('AccountDeleted')])->withInput()
                        ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);
                }

                session(['CurrentCompany' => $company]);

                if (!has_valid_cert()) {
                    return redirect()->action('Company\AuthController@certificates');
                }


                if (empty ($_SERVER['SSL_CLIENT_S_DN_CN'])) {
                    session(['SSL_CLIENT' => null]);

                    Auth::logout();
                    \Session::flush();
                    return redirect()->action('Company\AuthController@login')
                        ->withErrors(['LoginFailed' => __('Your certificate is invalid or expired')])
                        ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);
                }

                $sslClientSDnCn = $_SERVER['SSL_CLIENT_S_DN_CN'];

                $cert = Certificate::whereCompanyId($company->id)
                    ->whereStatus(Certificate::STATUS_DIVIDED_TO_DEVICE)
                    ->where('ssl_client_s_dn_cn', '=', $sslClientSDnCn)
                    ->first();

                if (empty ($cert)) {
                    Auth::logout();
                    \Session::flush();
                    return redirect()->action('Company\AuthController@login')
                        ->withErrors(['LoginFailed' => __('Your certificate is invalid or expired')])
                        ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);

                }

                $currentLoginToken = str_random(40);
                session(['CurrentLoginToken' => $currentLoginToken]);
                $staff->current_login_token = $currentLoginToken;

                $staff->last_access_at = current_timestamp();
                if ($staff->status == Staff::STATUS_UNREGISTER) {
                    $staff->status = Staff::STATUS_REGISTER;
                }
                $staff->save();

                return redirect()->action('Company\CompaniesController@getIndex');
            } else {
                if (!empty($_SERVER['SSL_CLIENT_S_DN_CN'])) {
                    $certCode = $_SERVER['SSL_CLIENT_S_DN_CN'];
                    $companyId = Certificate::getCert($certCode, 'company_id');

                    $staff = Staff::where('company_id', $companyId)->where('username', $request['username'])->where('account_type', Staff::ACCOUNT_TYPE_COMPANY)->first();
                    if (!empty($staff)) {
                        if ($staff->status == Staff::STATUS_UNREGISTER || $staff->status == Staff::STATUS_REGISTER) {

                            if ($staff->number_login_retry <= 1) {
                                $staff->number_login_retry = 0;
                                $staff->status = Staff::STATUS_LOCKOUT;
                            } else {
                                $staff->number_login_retry = $staff->number_login_retry - 1;
                            }
                            $staff->save();

                        }
                        if ($staff->status == Staff::STATUS_LOCKOUT) {
                            return redirect()->action('Company\AuthController@login')
                                ->withErrors(['LoginFailed' => __('Account has been lockout. Please contact with Mediaid to get a new password.')])
                                ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);
                        }
                    }
                }


                return redirect()->action('Company\AuthController@login')
                    ->withErrors(['LoginFailed' => __('InvalidUsernameOrPassword')])
                    ->withInput(['username' => $request->input('username'), 'company_id' => $request['company_id']]);
            }
        }


        return view('company.auth.login');

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function certificates(Request $request)
    {
        if (!Auth::user()) {
            return redirect()->action('Company\AuthController@login');
        }

        $companyId = $this->getCurrentCompany('id');
        if ($request->isMethod('post')) {

            $next = $request->input('next');
            $prev = $request->input('prev');

            // first step: user enter store id
            if ($next === 'certificates_issue') {
                // second step: user select a certificate
                $certAlias = $request->input('cert_alias');


                $cert = Certificate::where('alias', '=', $certAlias)
                    ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
                    ->whereNull('store_id')
                    ->where('company_id', '=', $companyId)
                    ->where('status', '=', Certificate::STATUS_DIVIDED_TO_STORE)
                    ->first();

                if (empty ($cert)) {
                    return redirect()->action('Company\AuthController@certificates');
                }

                return view('company.auth.certificates_issue', [
                    'cert' => $cert,
                    'jsonData' => [
                        'securedLoginUrl' => action('Company\AuthController@securedLogin')
                    ]
                ]);

            } else if ($next === 'export_cert') {
                // last step: user downloads certificate and install on current pc
                $certAlias = $request->input('cert_alias');

                $name = $request->input('cert_name');

                if (!$name) {
                    throw new \Exception('Certificate name cant not be empty');
                }

                $cert = Certificate::where('alias', '=', $certAlias)
                    ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
                    ->whereNull('store_id')
                    ->where('company_id', '=', $companyId)
                    ->where('status', '=', Certificate::STATUS_DIVIDED_TO_STORE)
                    ->first();

                if (empty ($cert)) {
                    return redirect()->action('Company\AuthController@certificates');
                }

                $cert->name = $name;
                $cert->status = Certificate::STATUS_DIVIDED_TO_DEVICE;
                $cert->issued_to_device_at = current_timestamp();
                $cert->save();

                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false); // required for certain browsers


                header('Content-Disposition: attachment; filename="cert_' . $companyId . '_' . $cert->ssl_client_s_dn_cn . '.p12";');
                header('Content-Transfer-Encoding: binary');
                //header('Content-Length: ' . filesize($filename));

                echo base64_decode($cert->client_pkcs12_certificate);

                exit;

            }

        }

        $certs = Certificate::where('company_id', '=', $companyId)
            ->whereIsMediaid(Certificate::IS_NOT_MEDIAID)
            ->whereNull('store_id')
            ->whereIn('status', [Certificate::STATUS_DIVIDED_TO_STORE, Certificate::STATUS_DIVIDED_TO_DEVICE])
            ->get();

        return view('company.auth.certificates_list', ['certs' => $certs]);
    }

    /**
     * Client certificate is required when user accesses /auth/secured-login
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function securedLogin(Request $request)
    {

        session(['SSL_CLIENT' => true]);

        Auth::logout();
        return $this->login($request);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        \Session::flush();

        return redirect()->action('Company\AuthController@login');
    }

    /**
     * Changes current staff password if must_change_password = 0
     * @param Request $request
     * @throws \Exception
     * @return View
     */
    public function changePassword(Request $request)
    {
        $staff = Auth::user();

        $companySetting = Staff::getLoginSettingCompany();
        $compareDay = $companySetting->password_expire;
        $changePassDay = (int)$compareDay;

        $companyCreated = Staff::select('last_change_password')
            ->where('company_id', '=', $this->getCurrentCompany('id'))
            ->where('username', '=', $staff->username)
            ->get()->first();

        $datetime1 = new DateTime(date('Y-m-d', time()));
        $datetime2 = new DateTime(date('Y-m-d', strtotime($companyCreated['last_change_password'])));
        $interval = $datetime2->diff($datetime1)->days;

        $haveToChangePass = $interval < $changePassDay;

        if (!$staff) {
            return redirect()->action('Company\AuthController@login');
        }

        if (!$staff->must_change_password && $interval < $changePassDay) {
            return redirect()->action('Company\CompaniesController@getIndex');
        }

        if ($request->isMethod('post')) {
            $password = $request->input('password');

            if (strlen($password) < 6 || !preg_match('/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/', $password)) {
                throw new \Exception('Invalid password');
            }


            $passwordStaff = PasswordStaff::where('staff_id', $staff->id)->where('type', PasswordStaff::ACC_TYPE_MANAGEMENT)->where('password', $password)->first();
            if (empty($passwordStaff)) {
                PasswordStaff::create(['staff_id' => $staff->id, 'type' => PasswordStaff::ACC_TYPE_MANAGEMENT, 'times' => 1, 'last_change_password' => date('Y-m-d H:i:s'), 'password' => $password]);
            } else {
                if ($passwordStaff->times >= 5) {
                    return redirect()->action('Company\AuthController@changePassword')
                        ->withErrors(['changePassFails' => __('Password was used too many times allowed')])
                        ->withInput(['password' => $password]);
                } else {
                    $passwordStaff->times = $passwordStaff->times + 1;
                    $passwordStaff->save();
                }
            }


            $staff->password = Hash::make($password);
            $staff->must_change_password = 0;
            $staff->last_change_password = current_timestamp();

            if ($staff->save()) {
                return redirect()->action('Company\CompaniesController@getIndex');
            } else {
                throw new \Exception('Could not update password');
            }

        }

        return view('company.auth.change_password', compact('haveToChangePass', 'companyCreated'));
    }


    /**
     *
     */
    public function profile()
    {
        if (!Auth::user()) {
            return redirect()->action('Company\AuthController@login');
        }

        return view('company.auth.profile', ['staff' => Auth::user()]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function updateProfile(Request $request)
    {
        $staff = Auth::user();

        $cancelable = $request->input('prev') !== null;

        if (!$staff) {
            return redirect()->action('Home\AuthController@login');
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'first_name_kana' => 'required',
                'last_name_kana' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect($request->getUri())
                    ->withErrors($validator)
                    ->withInput();
            }

            $staff->first_name = $request->input('first_name');
            $staff->last_name = $request->input('last_name');
            $staff->first_name_kana = $request->input('first_name_kana');
            $staff->last_name_kana = $request->input('last_name_kana');
            $staff->department = $request->input('department');


            if ($staff->save()) {
                return redirect()->action('Company\CompaniesController@getIndex');
            } else {
                throw new \Exception('Could not update profile');
            }
        }
        $jobCategory = Staff::$jobCategory;

        return view('company.auth.update_profile', ['cancelable' => $cancelable, 'jsonData' => ['staff' => $staff, 'cancelable' => $cancelable, 'jobCategory' => $jobCategory]]);
    }

}