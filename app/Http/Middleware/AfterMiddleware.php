<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\SystemControllers;
use App\Http\Controllers\AppController;
use App\Models\AccessLog;
use App\Models\AccessTokenWinApp;
use App\Models\AppModel;
use App\Models\LoginLog;
use App\Models\Staff;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\DB;

class AfterMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    protected $loginAction = [
        'App\Http\Controllers\Home\AuthController@login',
        'App\Http\Controllers\Company\AuthController@login',
        'App\Http\Controllers\Mediaid\AuthController@login',
        'App\Http\Controllers\Api\OAuthTokensController@getAccessToken',
        'App\Http\Controllers\WinApi\UsersController@postLogin'
    ];

    protected $autoLogin = 'App\Http\Controllers\Api\SystemControllers@getGlobalSettings';

    protected $logoutAction = [
        'App\Http\Controllers\Home\AuthController@logout',
        'App\Http\Controllers\Company\AuthController@logout',
        'App\Http\Controllers\Mediaid\AuthController@logout',
        'App\Http\Controllers\Api\OAuthTokensController@postLogout',
    ];

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
        DB::connection()->enableQueryLog();
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'IP' => $request->ip()
        ];
        if($this->auth->user()){
            $log['account_id'] = $this->auth->user()->id;
        }
        $response = $next($request);
        $action = $request->route()->getAction();
        //Check enviroment
        $uses = explode('\\', $action['uses']);
        if (in_array('Api', $uses)) {
            $account_type = User::ACCOUNT_TYPE_USER;
            $log['sourcetype'] = AppModel::getOS();
            $log['target'] = User::USER_ACCESS_NAME;
        } elseif (in_array('WinApi', $uses)) {
            $account_type = Staff::ACCOUNT_TYPE_STORE;
            $log['sourcetype'] = AppModel::getOS();
            $log['target'] = Staff::STORE_ACCESS_API_NAME;
        } else {
            $log['sourcetype'] = AppModel::getBrowser();
            if(in_array('Home', $uses)){
                $account_type = Staff::ACCOUNT_TYPE_STORE;
                $log['target'] = Staff::STORE_ACCESS_NAME;
            }elseif(in_array('Company', $uses)){
                $account_type = Staff::ACCOUNT_TYPE_COMPANY;
                $log['target'] = Staff::COMPANY_ACCESS_NAME;
            }elseif(in_array('Mediaid', $uses)){
                $account_type = Staff::ACCOUNT_TYPE_MEDIAID;
                $log['target'] = Staff::MEDIAID_ACCESS_NAME;
            }
        }

        //Auto login save log
        if($action['uses'] == $this->autoLogin AND $account_type == User::ACCOUNT_TYPE_USER){
            if(!empty($response->original['data']) AND !empty($response->original['data']['loginAuto'] AND $response->original['data']['loginAuto']->auto)){
                $user = User::findByAlias($request->token['user_alias']);
                if(!empty($user)){
                    $log['input_id'] = $user->email;
                    $log['account_id'] = $user->id;
                    $log['login_result'] = LoginLog::SUCCESS_AUTO_LOGIN_LOG;
                    $log['account_type'] = $account_type;
                    if($log['input_id'] != ''){
                        $login_log = new LoginLog();
                        $login_log->fill($log);
                        $login_log->save();
                    }
                }
            }
        }

        $is_access_log = true;
        if ($request->isMethod('post')) {
            //Check is login log or access log
            $type_log = '';
            if (in_array($action['uses'], $this->loginAction))
            {
                $is_access_log = false;
                $log['input_id'] = $request['username'] ? : '';
                $log['account_type'] = $account_type;
                $login_log = new LoginLog();
                if($log['target'] == User::USER_ACCESS_NAME){
                    if(!empty($response->original['data']['user'])){
                        if($response->original['data']['user']->email != 'anonymous'){
                            $log['login_result'] = LoginLog::SUCCESS_LOGIN_LOG;
                            $log['account_id'] = $response->original['data']['user']['id'];
                        }
                    }else{
                        $log['login_result'] = LoginLog::FAIL_LOGIN_LOG;
                    }
                }else if($log['target'] == Staff::STORE_ACCESS_API_NAME) {
                    if(!empty($response->original['data']) AND !empty($response->original['data']['access_token'])){
                        $access = AccessTokenWinApp::where('access_token', $response->original['data']['access_token'])->get();
                        if(!empty($access)){
                            $user = Staff::where('company_id', $access->company_id)->where('username', $access->staff_id)->first();
                            if(!empty($user)){
                                $log['account_id'] = $user->id;
                                $log['login_result'] = LoginLog::SUCCESS_LOGIN_LOG;
                            }
                        }
                    }
                }else{
                    if($this->auth->user()){
                        $log['login_result'] = LoginLog::SUCCESS_LOGIN_LOG;
                        $log['account_id'] = $this->auth->user()->id;
                    }else{
                        $log['login_result'] = LoginLog::FAIL_LOGIN_LOG;
                    }
                }
                if($log['input_id'] != ''){
                    $login_log->fill($log);
                    $login_log->save();
                }
            }
        }

        if($is_access_log){
            $log['access_function'] = $request->route()->getActionName();
            if($log['target'] == User::USER_ACCESS_NAME){
                if(!empty($request->token['user_alias']) AND $request->token['user_alias'] != ''){
                    $log['account_id'] = User::findByAlias($request->token['user_alias'], 'id') ? : '';
                }
            }else if($log['target'] == Staff::STORE_ACCESS_API_NAME) {

            }else{
                if($this->auth->user()){
                    $log['account_id'] = $this->auth->user()->id;
                }
            }
            if($account_type == User::ACCOUNT_TYPE_USER){
                $system = new SystemControllers();
                $check = $system->getGlobalSettings($request);
                if(!empty($check['data']) AND !empty($check['data']['loginAuto']) AND $check['data']['loginAuto']->auto){
                    $log['is_auto_login'] = AccessLog::SUCCESS_AUTO_LOGIN;
                }
            }
            $queries = DB::getQueryLog();
//            echo '<pre>'; var_dump($queries); echo '</pre>'; die;
            if(!empty($queries)){
                foreach($queries as $query){
                    if(strpos($query['query'], 'insert into ') !== false){
                        $access_action = AppModel::ACCESS_ADD_ACTION;
                        if(isset(explode('insert into', strtolower($query['query']))[1]) AND isset(explode(' ' , explode('insert into', strtolower($query['query'])) [1])[1]) AND isset(explode('`', explode(' ' , explode('insert into', strtolower($query['query'])) [1])[1])[1])){
                            $access_data_type = explode('`', explode(' ' , explode('insert into', strtolower($query['query'])) [1])[1])[1];
                        }
                    }elseif(strpos($query['query'], 'update ') !== false){
                        $access_action = AppModel::ACCESS_EDIT_ACTION;
                        if(isset(explode('update', strtolower($query['query']))[1]) AND isset(explode(' ' , explode('update', strtolower($query['query'])) [1])[1]) AND isset(explode('`', explode(' ' , explode('update', strtolower($query['query'])) [1])[1])[1])){
                            $access_data_type = explode('`', explode(' ' , explode('update', strtolower($query['query'])) [1])[1])[1];
                        }
                    }elseif(strpos($query['query'], 'delete ') !== false){
                        $access_action = AppModel::ACCESS_DELETE_ACTION;
                        if(isset(explode('delete', strtolower($query['query']))[1]) AND isset(explode(' ' , explode('delete', strtolower($query['query'])) [1])[2]) AND isset(explode('`', explode(' ' , explode('delete', strtolower($query['query'])) [1])[2])[1])){
                            $access_data_type = explode('`', explode(' ' , explode('delete', strtolower($query['query'])) [1])[2])[1];
                        }
                    }else{
                        $access_action = AppModel::ACCESS_SHOW_ACTION;
                        if(isset(explode(' from', strtolower($query['query']))[1]) AND isset(explode(' ' , explode(' from', strtolower($query['query'])) [1])[1]) AND isset(explode('`', explode(' ' , explode(' from', strtolower($query['query'])) [1])[1])[1])){
                            $access_data_type = explode('`', explode(' ' , explode(' from', strtolower($query['query'])) [1])[1])[1];
                        }
                    }
                    if(count($query['bindings']) > 0){
                        $list_bindings = explode('?', $query['query']);
                        foreach($list_bindings as $key => $text){
                            if(strpos($text . '?', '`id` = ?') !== false){
                                $access_data_id = $query['bindings'][$key];
                            }elseif(strpos($text, '`id` in(') !== false){
                                $access_data_id = explode(',', explode(')' , explode('`id` in (', $query['query'])[1])[0]);
                            }
                        }
                    }
                    if(!empty($access_data_type)){
                        $log['access_data_type'] = $access_data_type;
                        unset($access_data_type);
                    }
                    if(!empty($access_data_id)){
                        $log['access_data_id'] = json_encode($access_data_id);
                        unset($access_data_id);
                    }
                    if(!empty($access_action)){
                        $log['access_action'] = $access_action;
                        unset($access_action);
                    }
                    $log['access_result'] = AccessLog::SUCCESS_ACCESS_LOG;
                    if(!empty($log['access_data_type'])){
                        $access_log = new AccessLog();
                        $access_log->fill($log);
                        $access_log->save();
                        unset($access_log);
                    }
                }
            }
            if(in_array($action['uses'], $this->logoutAction)){
                //Logout save log to access log
                if(!empty($log['account_id'])){
                    $log['access_result'] = AccessLog::SUCCESS_ACCESS_LOG;
                    $access_log = new AccessLog();
                    $access_log->fill($log);
                    $access_log->save();
                    unset($access_log);
                }else{
                    if(!empty($response->original['msg']) AND $response->original['msg'] == 'Successfully'){
                        if(!empty($request->token['user_alias']) AND $request->token['user_alias'] != ''){
                            $log['account_id'] = User::findByAlias($request->token['user_alias'], 'id') ? : '';
                            $log['access_result'] = AccessLog::SUCCESS_ACCESS_LOG;
                            $access_log = new AccessLog();
                            $access_log->fill($log);
                            $access_log->save();
                            unset($access_log);
                        }
                    }
                }
            }
        }
        return $response;
    }
}
