<?php

namespace App\Http\Controllers\Mediaid;
use App\Models\Order;
use App\Models\Photo;
use App\Models\MessageTemplate;
use App\Models\Message;
use Illuminate\Http\Request;
use DB;

class OrdersController extends MediaidAppController
{
	public function getIndex(){

        $searchData = session('OrderSearchData');

        if ($searchData) {
            return $this->getOrderData($searchData);
        }
       
        //$companyId = $this->getCurrentCompany('id');

        $orderQuery = Order::join('users', 'users.id', '=', 'orders.user_id')
                           ->join('stores','stores.id','=', 'orders.store_id')
                           ->join('companies','companies.id','=','orders.company_id')
            			   ->select('orders.*','stores.name','companies.name as company_name',
            			   	        'users.first_name','users.first_name_kana',
            			   	        'users.last_name_kana','users.last_name', 
            			   	        'users.status as user_status', 'users.alias AS user_alias');

        $paginate = $orderQuery->orderBy('orders.id', 'desc')->paginate(10);
        //dd($paginate);

        $orders = Order::render($paginate);

		return view('mediaid.orders.index',compact('paginate'));
	}

	public function postIndex(Request $request){

        if (isset ($request->all()['btn_reset']) ){
            session(['OrderSearchData' => null]);
            return redirect()->to(action('Mediaid\OrdersController@getIndex'));
        }

        session(['OrderSearchData' => $request->all()]);

        return redirect()->to(action('Mediaid\OrdersController@getIndex'));
    }

    public function getPhotos($alias = null) {
        $order =  $order = Order::select('orders.*', 'users.first_name', 'users.last_name','users.first_name_kana','users.last_name_kana', 'users.email', 'users.phone_number')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.alias', '=', $alias)
            //->where('orders.company_id', '=', $this->getCurrentCompany('id'))
            ->first();

        if (empty ($order)) {
            throw new \Exception('Order not found');
        }

        $order->order_code = Order::parseOrderCode($order->order_code,'-');
        $order->visit_at_string = Order::parseVisitTimeString($order->visit_at_string, $order->visit_at);


        $photos = Photo::select('photos.id', 'photos.photo_url','photos.alias')
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

        return view("mediaid.orders.photo", ['photos' => $photos->toArray(), 'order' => $order]);
    }

    private function getOrderData($request) {

        $orderQuery = Order::join('users', 'users.id', '=', 'orders.user_id')
                           ->join('stores','stores.id','=', 'orders.store_id')
                           ->join('companies','companies.id','=','orders.company_id')
                           ->select('orders.*','stores.name','companies.name as company_name',
                           			'users.first_name_kana','users.last_name_kana',
                           			'users.first_name', 'users.last_name', 
                           			'users.status as user_status', 'users.alias AS user_alias');
          
        if(!empty($request['store_name'])){
            $orderQuery = $orderQuery->search(DB::raw('stores.name'), $request['store_name']);
        }

        if(!empty($request['company_name'])){
            $orderQuery = $orderQuery->search(DB::raw('companies.name'), $request['company_name']);
        }

        if (!empty ($request['order_code'])) {
            $orderQuery = $orderQuery->search('orders.order_code', $request['order_code']);
        }

        if (@$request['status'] != -1) {
            $orderQuery = $orderQuery->where('orders.status', '=' , $request['status']);
        }

        $receivedDateStart = parse_start_date(@$request['received_date_start'], @$request['received_time_start']);
        $receivedDateEnd = parse_end_date(@$request['received_date_end'], @$request['received_time_end']);

        if ($receivedDateStart) {
            $orderQuery = $orderQuery->where('orders.created_at', '>=', $receivedDateStart);
        }

        if ($receivedDateEnd) {
            $orderQuery = $orderQuery->where('orders.created_at', '<=', $receivedDateEnd);
        }

        $paginate = $orderQuery->orderBy('orders.id', 'desc')->paginate(10);
        //dd($paginate);

        $orders = Order::render($paginate);

        return view('mediaid.orders.index', ['paginate' => $paginate,'search' => $request]);
    }
}