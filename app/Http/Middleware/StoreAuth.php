<?php

namespace App\Http\Middleware;

use App\Models\Certificate;
use App\Models\Store;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Staff;
use App\Models\Company;

class StoreAuth
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        } else {

            $staff = $this->auth->user();
            /*
            $sslClientSDnCn = $_SERVER['SSL_CLIENT_S_DN_CN'];
            $cert = Certificate::whereCompanyId($staff->company_id)
                ->whereStatus(Certificate::STATUS_DIVIDED_TO_DEVICE)
                ->where('ssl_client_s_dn_cn', '=', $sslClientSDnCn)
                ->first();

            if (empty ($cert)) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Home\AuthController@login');

            }
            */

            $store = $store = Store::whereCompanyId($staff->company_id)
                ->where('is_published', '=', 1)
                ->where(function ($query) {
                    return $query->where('is_deleted', '=', 0)->orWhereNull('is_deleted');
                })
                ->whereId(\App\Models\Store::current('id'))->first();

            if (empty ($store)) {
                $this->auth->logout();
                return redirect()->action('Home\AuthController@login');
            }

            if ($staff->account_type !== Staff::ACCOUNT_TYPE_STORE) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Home\AuthController@login');
            }
            if ($staff->status == Staff::STATUS_ACCOUNT_LOCK || $staff->status == Staff::STATUS_LOCKOUT) {
                $this->auth->logout();
                \Session::flush();

                return redirect()->action('Home\AuthController@login')->withErrors(['LoginFailed' => __('AccountLogout')])->withInput();
            }
            if ($staff->status == Staff::STATUS_DELETED) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Home\AuthController@login')->withErrors(['LoginFailed' => __('AccountLogout')])->withInput();
            }

            //Check company
            $company = Company::where('id', $staff->company_id)->first();
            if (!empty($company)) {
                if ($company->status == \App\Models\Company::STATUS_CANCELLATION_COMPLETED) {
//                    die('xung nhi');
                    $this->auth->logout();
                    \Session::flush();

                    return redirect()->action('Home\AuthController@login');
                }
            }

            if (!has_valid_cert()) {
                return redirect()->action('Home\AuthController@certificates');
            }

            if (!has_active_cert()) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Home\AuthController@login');
            }

            if (session('CurrentStore') == null) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Home\AuthController@login');
            }

            // Validates last change password
            $currentTime = current_timestamp();
            $loginExpireTime = (int)env('LOGIN_EXPIRE_TIME', 15);
            $loginSetting = Staff::getLoginSetting();

            if (get_minutes_between($currentTime, $staff->last_access_at) >= $loginExpireTime) {
                $this->auth->logout();
                \Session::flush();
                if (!$request->ajax()) {
                    return redirect()->action('Home\AuthController@login');
                } else {
                    return r_err('You have been logged out', 'Logout', 401);
                }
            }

            if ($staff->must_change_password || get_days_between($currentTime, $staff->last_change_password) >= (int)$loginSetting->password_expire) {
                return redirect()->action('Home\AuthController@changePassword');
            }

            // Validates double login
            if (!$loginSetting->multi_account_login && $staff->current_login_token != session('CurrentLoginToken')) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Home\AuthController@login');
            }

            if (empty ($staff->first_name)
                || empty ($staff->last_name)
                || empty ($staff->first_name_kana)
                || empty ($staff->last_name_kana)
            ) {

                return redirect()->action('Home\AuthController@updateProfile');
            }

            if (!$request->ajax()) {
                $staff->last_access_at = current_timestamp();
                $staff->save();
            }

        }

        return $next($request);
    }
}
