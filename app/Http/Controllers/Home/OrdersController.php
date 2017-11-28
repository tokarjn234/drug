<?php

namespace App\Http\Controllers\Home;


use App\Models\Order;
use App\Models\Photo;
use App\Models\MessageTemplate;
use Illuminate\Http\Request;
use DB;
use Auth;

class OrdersController extends HomeAppController
{

    /**
     * Orders list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request) {

        $searchData = session('OrderSearchData');

        if ($searchData) {
            return view('home.orders.index', $this->getOrderData($searchData));
        }
		
		$storeId = $this->getCurrentStore('id');
		$companyId = $this->getCurrentCompany('id');

        $orderQuery = Order::join('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.first_name', 'users.last_name','users.first_name_kana', 'users.last_name_kana' , 'users.status as user_status', 'users.alias AS user_alias')
            ->where('orders.store_id', '=', $storeId)
            ->where('orders.company_id', '=', $companyId);

        $messageTemplates = MessageTemplate::getDefaultMessageTemplates($storeId, $companyId);


        $paginate = $orderQuery->orderBy('orders.id', 'desc')->paginate(10);

        $orders = Order::render($paginate);

        return view('home.orders.index', [
            'paginate' => $paginate,
            'jsonData' => [
                'orders' => $orders,
                'messageTemplates' => $messageTemplates,
                'sendMsgUrl' => action('Home\MessagesController@postSendMessage'),
                'msgUrl' => action('Home\MessagesController@getIndex', ['ordering' => 1, 'page' => $paginate->toArray()['current_page']]),
                'completeOrderUrl' => action('Home\OrdersController@postCompleteOrder'),
                'deleteOrderUrl' => action('Home\OrdersController@postDeleteOrder'),
                'photoUrl' => action('Home\OrdersController@getPhotos'),
				'storeName' => $this->getCurrentStore('name')

            ]
        ]);
    }

    /**
     * Orders list
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postIndex(Request $request) {

        if ($request->input('_clear')) {
            session(['OrderSearchData' => null]);
            return redirect()->to(action('Home\OrdersController@getIndex'));
        }

        session(['OrderSearchData' => $request->all()]);

        return redirect()->to(action('Home\OrdersController@getIndex'));
    }

    /**
     * Gets order data
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function getOrderData($request) {
        $storeId = $this->getCurrentStore('id');
        $companyId = $this->getCurrentCompany('id');

        $messageTemplates = MessageTemplate::getDefaultMessageTemplates($storeId, $companyId);

        $orderQuery = Order::join('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*', 'users.first_name', 'users.last_name','users.first_name_kana', 'users.last_name_kana' ,'users.status as user_status', 'users.alias AS user_alias')
            ->where('orders.store_id', '=', $storeId)
            ->where('orders.company_id', '=', $companyId);

        $orderNotPagination = $orderQuery->lists('alias');

        if (!empty ($request['order_code'])) {
            $orderQuery = $orderQuery->search(DB::raw('SUBSTRING_INDEX(order_code,\'-\',-2)'), $request['order_code']);
        }

        if (!empty ($request['username'])) {
            $orderQuery = $orderQuery->searchEncrypted('full_name_index', $request['username']);
        }

        if (@$request['status'] != -1 && empty ($request['completed_flag']) && empty($request['status_invalid'])) {
            $orderQuery = $orderQuery->where('orders.status', '=' , $request['status']);
        }

        if (@$request['completed_flag'] === 'false') {
            $orderQuery = $orderQuery->where(function($query) {
                $query->where('completed_flag', '=', 0)
                    ->orWhereNull('completed_flag');
            });

        }

        if (@$request['status_invalid'] === 'false') {
            $orderQuery = $orderQuery->where('orders.status', '!=', Order::STATUS_INVALID);
        }


        $receivedDateStart = parse_start_date(@$request['received_date_start'], @$request['received_time_start']);
        $receivedDateEnd = parse_end_date(@$request['received_date_end'], @$request['received_time_end']);
        $visitDateStart = parse_start_date(@$request['visit_date_start'], @$request['visit_time_start']);
        $visitDateEnd = parse_end_date(@$request['visit_date_end'], @$request['visit_time_end']);

        if ($receivedDateStart) {
            $orderQuery = $orderQuery->where('orders.created_at', '>=', $receivedDateStart);
        }

        if ($receivedDateEnd) {
            $orderQuery = $orderQuery->where('orders.created_at', '<=', $receivedDateEnd);
        }

        if ($visitDateStart) {
            $orderQuery = $orderQuery->where('orders.visit_at', '>=', $visitDateStart);
        }

        if ($visitDateEnd) {
            $orderQuery = $orderQuery->where('orders.visit_at', '<=', $visitDateEnd);
        }

        $paginate = $orderQuery->orderBy('orders.id', 'desc')->paginate(10);

        $orders = Order::render($paginate);

        return [
            'paginate' => $paginate,
            'search' => $request,
            'jsonData' => [
                'ordersNotPagination' => $orderNotPagination,
                'orders' => $orders,
                'messageTemplates' => $messageTemplates,
                'sendMsgUrl' => action('Home\MessagesController@postSendMessage'),
                'msgUrl' => action('Home\MessagesController@getIndex', ['ordering' => 1]),
                'completeOrderUrl' => action('Home\OrdersController@postCompleteOrder'),
                'deleteOrderUrl' => action('Home\OrdersController@postDeleteOrder'),
                'photoUrl' => action('Home\OrdersController@getPhotos'),
				'storeName' => $this->getCurrentStore('name'),


            ]
        ];
    }

    /**
	 * Complete an order
     * @param Request $request
     * @return array
     */
    public function postCompleteOrder(Request $request) {
        $order = Order::findByAlias($request->input('order_alias'));
        $now = time();
        $current = date("Y-m-d H:i:s", $now);

        if (!$order) {
            return r_err('Order not found');
        }
		
		if ($order->status == Order::STATUS_INVALID) {			
			return r_err(__('CanNotSetCompleteAnInvalidItem'));
		}
		
        $completedFlag = $request->input('completed') ? 1 : 0;

        if ($completedFlag) {
            $status = Order::STATUS_PREPARED_NOTIFIED;
        } else {
            $current = NULL;
            if (empty ($order->sent_received_msg_at)) {
                $status= Order::STATUS_RECEIVED;
            } else {
                $status= Order::STATUS_RECEIVED_NOTIFIED;

            }
        }

        $result = DB::update('UPDATE `orders` SET `completed_flag`=?,`status`=?, `update_staff_id`=?, `completed_at`=? WHERE `id`=?', [$completedFlag,$status, $this->getCurrentStaff('id'), $current, $order['id']]);

        if ($result) {
            return r_ok(['status' => $status, 'completed_flag' => $completedFlag, '$status' => @Order::$statuses[$status]]);
        }

        return r_err('Something went wrong!');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function postDeleteOrder(Request $request) {
        $order = Order::findByAlias($request->input('order_alias'));
		
		if (!$order) {
			return r_err('Order not found');
		}
		
		if ($order->status == Order::STATUS_INVALID) {
			return r_err(__('CanNotDeleteAnInvalidItem'));
		}
		
		if ($order->completed_flag == 1) {
			return r_err(__('CanNotDeleteACompleteItem'));
		}		
		
        $result = DB::update('UPDATE `orders` SET `status`=?, `update_staff_id`=?,`delete_reason`=?, `deleted_at`=NOW() WHERE `id`=?',
            [Order::STATUS_INVALID, $this->getCurrentStaff('id'), $request->input('reason'), $order->id]);

        return $result ? r_ok($result) : r_err('Something went wrong!');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPhotos($alias = null) {
        $order =  $order = Order::select('orders.*', 'users.first_name', 'users.last_name','users.first_name_kana','users.last_name_kana', 'users.email', 'users.phone_number')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.alias', '=', $alias)
            ->where('orders.company_id', '=', $this->getCurrentCompany('id'))
            ->where('orders.store_id', '=', $this->getCurrentStore('id'))
            ->first();

        if (empty ($order)) {
            throw new \Exception('Order not found');
        }

        $order->order_code = Order::parseOrderCode($order->order_code,'-');
        $order->visit_at_string = Order::parseVisitTimeString($order->visit_at_string, $order->visit_at);


        $photos = Photo::select('photos.id', 'photos.photo_url', 'photos.alias')
                        ->join('orders', 'photos.order_id', '=', 'orders.id')
                        ->where('orders.alias', '=', $alias)
                        ->get();

        if (empty ($photos)) {
            throw new \Exception('Order not found');
        }

        $order->drugbrand_change = !$order->drugbrand_change ? Order::$drugBrandChanges[0] : @Order::$drugBrandChanges[$order->drugbrand_change];
        $order->drugbook_use = !$order->drugbook_use? Order::$drugBrandUses[0] : @Order::$drugBrandUses[$order->drugbook_use];

        foreach ($photos as $photo) {
            $photo->photo_url = \HTML::image(action('Home\PhotosController@getView', ['alias' => $photo->alias]));
        }

        return view("home.orders.photo", ['photos' => $photos->toArray(), 'order' => $order]);
    }

}