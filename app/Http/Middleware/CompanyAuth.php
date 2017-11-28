<?php


namespace App\Http\Middleware;

use Closure;
use App\Models\Company;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Staff;
use App\Models\Setting;

class CompanyAuth
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
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {

        $this->auth = $auth;

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->action('Company\AuthController@login');
            }
        } else {

            $staff = $this->auth->user();
            $loginExpireTime = (int) env('LOGIN_EXPIRE_TIME', 15);

            if (get_minutes_between(current_timestamp(), $staff->last_access_at) >= $loginExpireTime) {
                $this->auth->logout();
                \Session::flush();
                if (!$request->ajax()) {
                    return redirect()->action('Company\AuthController@login');
                } else {
                    return r_err('You have been logged out', 'Logout', 401);
                }
            }

            if ($staff->account_type !== Staff::ACCOUNT_TYPE_COMPANY) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Company\AuthController@login');
            }

            if ($staff->status == Staff::STATUS_DELETED) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Company\AuthController@login')->withErrors(['LoginFailed' => __('AccountLogout')])->withInput();
            }

            if ($staff->status == Staff::STATUS_ACCOUNT_LOCK || $staff->status == Staff::STATUS_LOCKOUT) {
                $this->auth->logout();
                \Session::flush();
                
                return redirect()->action('Company\AuthController@login')->withErrors(['LoginFailed' => __('AccountLogout')])->withInput();
            }

            //Check company
            $company = Company::where('id', $staff->company_id)->first();
            if(!empty($company)){
                if($company->status==\App\Models\Company::STATUS_CANCELLATION_COMPLETED){
                    $this->auth->logout();
                    \Session::flush();
                    return redirect()->action('Company\AuthController@login');
                }
            }

            if (!has_valid_cert()) {
                return redirect()->action('Company\AuthController@certificates');
            }

            if(!has_active_cert()){
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Company\AuthController@login');
            }

            //change password 

            $currentTime = current_timestamp();
            $loginExpireTime = (int)env('LOGIN_EXPIRE_TIME', 15);
            $loginSetting = Staff::getLoginSettingCompany();

            if (get_minutes_between($currentTime, $staff->last_access_at) >= $loginExpireTime) {
                $this->auth->logout();
                \Session::flush();
                if (!$request->ajax()) {
                    return redirect()->action('Company\AuthController@login');
                } else {
                    return r_err('You have been logged out', 'Logout', 401);
                }
            }

            if ($staff->must_change_password || get_days_between($currentTime, $staff->last_change_password) >= (int)$loginSetting->password_expire) {
                return redirect()->action('Company\AuthController@changePassword');
            }

            if (!$request->ajax()) {
                $staff->last_access_at = current_timestamp();
                $staff->save();
            }


        }

        return $next($request);
    }
}