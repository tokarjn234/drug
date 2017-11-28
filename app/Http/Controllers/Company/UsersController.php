<?php

namespace App\Http\Controllers\Company;
use Auth;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use DB;
use Hash;

class UsersController extends CompanyAppController
{
/**
     * Gets users data
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request) {
        $searchData = session('CompanyUserSearchData');
		$csv = false;
		if(isset($_GET['csv']))
			$csv = $_GET['csv'];
        if ($searchData) {
            return $this->getUserData($searchData,$csv);
        }
        
    	$thisUser = Auth::user();
   
        $userQuery = User::leftJoin('orders', function($join) {
        	$join->on('users.id', '=', 'orders.user_id');
        })
        ->leftJoin('stores', function($join) {
        	$join->on('stores.id', '=', 'orders.store_id');
        })
        ->select(DB::raw("
            users.*,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`order_code` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_code`,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`created_at` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_created_at`,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`alias` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_alias`,
            stores.name AS `store_name`
            "))
        ->where('users.company_id','=',$thisUser->company_id)
        ->whereIn('users.status',[3,4,5,6])
        ->groupBy('users.id')->orderBy('users.id', 'desc');
        //dd($userQuery->get()->toArray());
        
        if($csv == true)
            return $this->getCsv(User::render($userQuery->get()->toArray()),true);
        
        $paginate = $userQuery->paginate(10);
        $users = User::render($paginate);

        return view('company.users.index', ['paginate' => $paginate, 'users' => $users]);
    }
    
    /**
     * Post request
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postIndex(Request $request) {
    	if (isset ($request->all()['btn_reset']) ){
            session(['CompanyUserSearchData' => null]);
            return redirect()->to(action('Company\UsersController@getIndex'));
        }

        session(['CompanyUserSearchData' => $request->all()]);

        return redirect()->to(action('Company\UsersController@getIndex'));
        
    }
    
    /**
     * Gets user detail
     * @param $alias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDetail($alias){
    	$thisUser = Auth::user();
    	$column = 'alias';
       	

        
        
        $user = User::
        leftJoin('orders', function($join) {
        	$join->on('users.id', '=', 'orders.user_id');
        })
        ->leftJoin('stores', function($join) {
        	$join->on('stores.id', '=', 'orders.store_id');
        })
        ->select(DB::raw("
            users.*,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`order_code` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_code`,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`created_at` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_created_at`,
            stores.name AS `store_name`
            "))
        ->where('users.alias','=',$alias)
        ->where('users.company_id','=',$thisUser->company_id)
        ->whereIn('users.status',[3,4,5,6])
        ->groupBy('users.id')->orderBy('users.id', 'desc')
        ->first();
        //dd($user);

       	$user = User::render($user, true);
       	
       	$userStores = User::
        join('orders', function($join) {
        	$join->on('users.id', '=', 'orders.user_id');
        })
        ->join('stores', function($join) {
        	$join->on('stores.id', '=', 'orders.store_id');
        })
        ->select(DB::raw("
            users.*,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`created_at` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_created_at`,
            stores.name AS `store_name`
            "))
        ->where('users.id','=',$user['id'])
        ->where('orders.status','!=',Order::STATUS_INVALID)  //! Delete order
        ->groupBy('stores.id')->orderBy('stores.id', 'desc')
        ->get()->toArray();
       	
        return view('company.users.detail',['user' => $user,'userStores' => $userStores]);

    }

    /**
     * Sets user log
     * @param $alias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStatusLock($alias){
    	$thisUser = Auth::user();

        User::where('users.alias','=',$alias)
        ->where('users.company_id','=',$thisUser->company_id)
        ->update(array('status' => 6));

        //return redirect()->action('Company\UsersController@getDetail', [$alias]);
        return redirect()->action('Company\UsersController@getIndex');
    }


    /**
     * Sets cancel user log
     * @param $alias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStatusCancelLock($alias){
        $thisUser = Auth::user();

        User::where('users.alias','=',$alias)
            ->where('users.company_id','=',$thisUser->company_id)
            ->update(array('status' => 3));

        return redirect()->action('Company\UsersController@getIndex');
    }


    /**
     * Sets user leave
     * @param $alias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getStatusLeave($alias){
    	$thisUser = Auth::user();

        $user = User::where('users.alias','=',$alias)->where('users.company_id','=',$thisUser->company_id);

        $user_data = $user->first();

        $update = [
            'status' => 5,
            'username' => NULL,
            'email' => NULL,
            'password' => NULL,
            'phone_number' => NULL,
            'first_name' => NULL,
            'last_name' => NULL,
            'first_name_kana' => NULL,
            'last_name_kana' => NULL,
            'postal_code' => NULL,
            'province' => NULL,
            'city1' => NULL,
            'address' => NULL
        ];

        if(empty($user_data['exited_at'])) {
            $update['exited_at'] = date("Y-m-d H:i:s");
        }

        $user->update($update);
        //return redirect()->action('Company\UsersController@getDetail', [$alias]);
        return redirect()->action('Company\UsersController@getIndex');
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
		    	__('UserStoreFinal'),
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
                    $user['store_name'],
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
    	//session(['UserSearchData' => null]);
    	$thisUser = Auth::user();
        $userQuery = User::
        join('orders', function($join) {
        	$join->on('users.id', '=', 'orders.user_id');
        })
        ->join('stores', function($join) {
        	$join->on('stores.id', '=', 'orders.store_id');
        })
        ->select(DB::raw("
            users.*,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`order_code` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_code`,
            SUBSTRING_INDEX(GROUP_CONCAT(orders.`created_at` ORDER BY orders.`id` DESC SEPARATOR '|'), '|', 1) AS `order_created_at`,
            stores.name AS `store_name`
            "))
        ->where('users.company_id','=',$thisUser->company_id)
        ->whereIn('users.status',[3,4,5,6])
        ->groupBy('users.id')->orderBy('users.id', 'desc');

        if (isset ($request['member_id']) && $request['member_id'] != '') {
            $userQuery = $userQuery->search('users.id' ,  $request['member_id']);
        }

        if (isset ($request['name']) && $request['name'] != '') {
            $userQuery = $userQuery->searchEncrypted('full_name_index', $request['name']);
        }
		
        if (isset ($request['name_kana']) && $request['name_kana'] != '') {
            $userQuery = $userQuery->searchEncrypted('full_name_kana_index', $request['name_kana']);
        }
        
    	if (isset ($request['gender']) && $request['gender'] != '') {
            $userQuery = $userQuery->where('users.gender', '=' ,  $request['gender']);
            $userQuery = $userQuery->whereIn('users.status',[3,6]);
        }
        
    	if (isset ($request['store_name']) && $request['store_name'] != '') {
            $userQuery = $userQuery->search('stores.name' ,  $request['store_name']);
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
        
    	if (isset ($request['month_birthday'])  && $request['month_birthday'] != '') {
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
        
     	if (isset ($request['order_code']) && $request['order_code'] != '') {
     		$userQuery = $userQuery->search('orders.order_code', $request['order_code']);
        }
        
    	if (isset ($request['is_member']) && $request['is_member'] == true) {
            $userQuery = $userQuery->where(function ($userQuery) {
	     		$userQuery = $userQuery->where('users.status', '!=' ,  User::STATUS_EXITED)
	     		->where('users.status', '!=' ,  User::STATUS_EXPELLED)
	     		->orWhere(DB::raw('users.status IS NULL'));
       		});
        }

        if($csv == true)
            return $this->getCsv(User::render($userQuery->get()->toArray()),true);

        $paginate = $userQuery->paginate(10);
        $users = User::render($paginate);

        return view('company.users.index', ['paginate' => $paginate, 'users' => $users,'search' => $request]);
    }

    // public function getOrderDetail($id = null){
    //     $messageOrder = order::join('users', 'users.id', '=', 'orders.user_id')
    //                          ->join('messages','messages.order_id','=','orders.id')
    //                          ->leftJoin('staffs','staffs.id','=','messages.created_staff_id')
    //                          ->select('orders.status','messages.*','staffs.first_name as staffFirstName','staffs.last_name as staffLastName','users.first_name', 'users.last_name', 'users.status as user_status', 'users.alias AS user_alias')
    //                          ->where('orders.alias','=',$id);

    //     //dd($messageOrder);
    //     $paginate = $messageOrder->paginate(10);
    //     //dd($paginate);
    //     //$messages = Message::render($paginate);

    //     if(isset($orderDetail) && isset($messageOrder) ){
    //         return view('company.orders.orderdetail',compact('orderDetail','messageOrder','paginate'));
    //     }else{
    //         return redirect()->to(action('Company\OrdersController@getOrderHistory'));
    //     }
    // }

}