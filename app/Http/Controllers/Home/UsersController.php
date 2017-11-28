<?php

namespace App\Http\Controllers\Home;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Hash;

class UsersController extends HomeAppController
{
	/**
     * Gets users data
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request) {
        $searchData = session('UserSearchData');
		$csv = false;

		if (isset($_GET['csv'])) {
            $csv = $_GET['csv'];
        }

        if ($searchData) {
            return $this->getUserData($searchData,$csv);
        }


        $userQuery = User::
        join('orders', function($join) {
        	$join->on('users.id', '=', 'orders.user_id');
        })
        ->select(DB::raw("
            users.*,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`order_code` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_code`,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`created_at` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_created_at`
            "))
        ->where('users.company_id','=', $this->getCurrentCompany('id'))
        ->whereIn('users.status',[3,4,5,6])
        ->where('orders.store_id', '=', $this->getCurrentStore('id'))
        ->groupBy('users.id')->orderBy('users.id', 'desc');

        if ($csv == true) {
            return $this->getCsv(User::render($userQuery->get()->toArray()),true);
        }


        $paginate = $userQuery->paginate(10);
        $users = User::render($paginate);
		
        return view('home.users.index', ['paginate' => $paginate, 'users' => $users]);
    }
    
    /**
     * Post request
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postIndex(Request $request) {

    	if (isset ($request->all()['btn_reset']) ){
            session(['UserSearchData' => null]);
            return redirect()->to(action('Home\UsersController@getIndex'));
        }

        session(['UserSearchData' => $request->all()]);

        return redirect()->to(action('Home\UsersController@getIndex'));
    }
    
    /**
     * Gets user detail
     * @param $alias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDetail($alias){

       	$user = User::
        join('orders', function($join) {
        	$join->on('users.id', '=', 'orders.user_id');
        })
        ->select(DB::raw("
            users.*,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`order_code` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_code`,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`created_at` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_created_at`
            "))
        ->where('users.alias','=',$alias)
        ->where('users.company_id', '=', $this->getCurrentCompany('id'))
        ->whereIn('users.status',[3,4,5,6])
        ->where('orders.store_id', '=', $this->getCurrentStore('id'))
        ->groupBy('users.id')
        ->orderBy('users.id', 'desc')
        ->first()->toArray();

       	$user = User::render($user, true);
       	
        return view('home.users.detail',['user' => $user]);

    }
    
	/**
     * Gets csv data
     * @param $users
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
	public function getCsv($users)
    {
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Encoding: UTF-8');
		header('Content-type: text/csv; charset=UTF-8');
		header("Content-Disposition: attachment; filename=data_user_".strtotime("now").".csv");
		
    	$handle = fopen('php://output', 'w');
    	fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));//utf-8 encoding
		fputcsv($handle, array(
		        __('UserMemberID'),
		    	__('UserMemberName'),	
		    	__('UserKanaName'),
		    	__('UserGender'),
		    	__('UserBirthday'),
		    	__('UserAge'),
		    	__('UserRegistrationDate'),
		    	__('UserTransmissionDate'),
		    	__('UserFinalVisitNoBr'),
		    	__('UserWithdrawal')
                
		));
		foreach ($users as $user) {
		    	//PR($user);die;
		        fputcsv($handle, array(
		            $user['id'],
		            $user['first_name'].' '.$user['last_name'],
		            $user['first_name_kana'].' '.$user['last_name_kana'],
		            $user['gender_csv'],
		            $user['birthday_csv'],
		            $user['age'],
		            $user['detail_created_at'],
                    $user['detail_order_created_at'],
		            $user['order_code'],
		            User::$is_checkout[$user['is_checkout']]
		        ));
		}
		
		fclose($handle);
		    //exit;

    }
    
    /**
     * Gets order data
     * @param $request, $csv
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function getUserData($request,$csv) {

        $userQuery = User::
        join('orders', function($join) {
        	$join->on('users.id', '=', 'orders.user_id');
        })
        ->select(DB::raw("
            users.*,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`order_code` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_code`,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`created_at` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_created_at`
            "))
        ->where('users.company_id','=', $this->getCurrentCompany('id'))
        ->whereIn('users.status',[3,4,5,6])
        ->where('orders.store_id', '=', $this->getCurrentStore('id'))
        ->groupBy('users.id')->orderBy('users.id', 'desc');

        if (!empty ($request['member_id'])) {
            $userQuery = $userQuery->search('users.id' ,  $request['member_id']);
        }

        if (!empty ($request['name'])) {
            $userQuery = $userQuery->searchEncrypted('full_name_index', $request['name']);
        }

        if (!empty ($request['name_kana'])) {
            $userQuery = $userQuery->searchEncrypted('full_name_kana_index', $request['name_kana']);
        }

        if (!empty ($request['gender'])) {
            $userQuery = $userQuery->where('users.gender', '=' ,  $request['gender']);
            $userQuery = $userQuery->whereIn('users.status',[3,6]);
        }
        
        $receivedBirthdayStart = parse_start_date(@$request['birthday_start']);
        $receivedBirthdayEnd = parse_end_date(@$request['birthday_end']);
        
    	if ($receivedBirthdayStart) {
            $userQuery = $userQuery->where('users.birthday', '>=', $receivedBirthdayStart);
            $userQuery = $userQuery->whereIn('users.status',[3,6]);
        }

        if ($receivedBirthdayEnd) {
            $userQuery = $userQuery->where('users.birthday', '<=', $receivedBirthdayEnd);
            $userQuery = $userQuery->whereIn('users.status',[3,6]);
        }

        if (!empty ($request['month_birthday'])) {
             $userQuery = $userQuery->where(DB::raw('MONTH(users.birthday)'), '=' ,$request['month_birthday']);
        }
        
     	$receivedOrderCreateStart = parse_start_date(@$request['order_created_date_start'],@$request['order_created_time_start']);
        $receivedOrderCreateEnd = parse_end_date(@$request['order_created_date_end'],@$request['order_created_time_end']);
        
    	if ($receivedOrderCreateStart) {
            $userQuery = $userQuery->where('orders.created_at', '>=', $receivedOrderCreateStart);
        }

        if ($receivedOrderCreateEnd) {
            $userQuery = $userQuery->where('orders.created_at', '<=', $receivedOrderCreateEnd);
        }

        if (!empty ($request['order_code'])) {
     		$userQuery = $userQuery->search('orders.order_code', $request['order_code']);
        }
        
    	if (isset ($request['is_member']) && $request['is_member'] == true) {
            $userQuery = $userQuery->where(function ($userQuery) {
	     		$userQuery = $userQuery->where('users.status', '!=' ,  User::STATUS_EXITED)
	     		->where('users.status', '!=' ,  User::STATUS_EXPELLED)
	     		->orWhere(DB::raw('users.status IS NULL'));
       		});
        }

        if ($csv == true) {
            return $this->getCsv(User::render($userQuery->get()->toArray()),true);
        }


        $paginate = $userQuery->paginate(10);
        $users = User::render($paginate);

        return view('home.users.index', ['paginate' => $paginate, 'users' => $users,'search' => $request]);
    }
}