<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Models\Staff;
use App\Models\Setting;

class MediaidAuth
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
                return redirect()->action('Mediaid\AuthController@login');
            }
        } else {
            $staff = $this->auth->user();
            $loginExpireTime = (int) env('LOGIN_EXPIRE_TIME', 15);

            if (get_minutes_between(current_timestamp(), $staff->last_access_at) >= $loginExpireTime) {
                $this->auth->logout();
                \Session::flush();
                if (!$request->ajax()) {
                    return redirect()->action('Mediaid\AuthController@login');
                } else {
                    return r_err('You have been logged out', 'Logout', 401);
                }
            }

            if ($staff->account_type !== Staff::ACCOUNT_TYPE_MEDIAID) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Mediaid\AuthController@login');
            }

            if ($staff->status == Staff::STATUS_ACCOUNT_LOCK || $staff->status == Staff::STATUS_LOCKOUT) {
                $this->auth->logout();
                \Session::flush();

                return redirect()->action('Mediaid\AuthController@login')->withErrors(['LoginFailed' => __('AccountLogout')])->withInput();
            }

            if ($staff->status == Staff::STATUS_DELETED) {
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Mediaid\AuthController@login')->withErrors(['LoginFailed' => __('AccountLogout')])->withInput();
            }

            if (!has_valid_cert()) {
                return redirect()->action('Mediaid\AuthController@certificates')->withErrors(['LoginFailed' => __('Your certificate is invalid or expired')])
                        ->withInput(['username' => $request->input('username')]);;
            }

            if(!has_active_mediaid_cert()){
                $this->auth->logout();
                \Session::flush();
                return redirect()->action('Mediaid\AuthController@login')->withErrors(['LoginFailed' => __('Your certificate is invalid or expired')])
                        ->withInput(['username' => $request->input('username')]);;
            }

            //change pass

            $currentTime = current_timestamp();
            $loginExpireTime = (int)env('LOGIN_EXPIRE_TIME', 15);
            $loginSetting = Staff::getLoginSettingMediaid();

            if (get_minutes_between($currentTime, $staff->last_access_at) >= $loginExpireTime) {

                $this->auth->logout();
                \Session::flush();
                if (!$request->ajax()) {
                    return redirect()->action('Mediaid\AuthController@login');
                } else {
                    return r_err('You have been logged out', 'Logout', 401);
                }
            }

            if ($staff->must_change_password || get_days_between($currentTime, $staff->last_change_password) >= (int)$loginSetting->password_expire) {
                return redirect()->action('Mediaid\AuthController@changePassword');
            }

            if (!$request->ajax()) {
                $staff->last_access_at = current_timestamp();
                $staff->save();
            }


        }

        return $next($request);
    }
}
