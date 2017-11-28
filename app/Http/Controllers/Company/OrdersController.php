<?php


namespace App\Http\Controllers\Company;
use App\Models\Order;
use App\Models\Photo;
use App\Models\MessageTemplate;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Http\Request;
use DB;
use DateTime;

class OrdersController extends CompanyAppController
{
    public function getOrderHistory(Request $request){

        $searchData = session('OrderSearchData');

        if ($searchData) {
            return $this->getOrderData($searchData);
        }


        $companyId = $this->getCurrentCompany('id');

        $orderQuery = Order::join('users', 'users.id', '=', 'orders.user_id')
                           ->join('stores','stores.id','=', 'orders.store_id')
            ->select('orders.*','stores.name','users.first_name','users.first_name_kana','users.last_name_kana','users.last_name', 'users.status as user_status', 'users.alias AS user_alias')

            ->where('orders.company_id', '=', $companyId);

        $paginate = $orderQuery->orderBy('orders.id', 'desc')->paginate(10);
        //dd($paginate);

        $orders = Order::render($paginate);

        return view('company.orders.orderhistory',compact('paginate'));
    }

    public function postOrderHistory(Request $request){

        if (isset ($request->all()['btn_reset']) ){
            session(['OrderSearchData' => null]);
            return redirect()->to(action('Company\OrdersController@getOrderHistory'));
        }

        session(['OrderSearchData' => $request->all()]);

        return redirect()->to(action('Company\OrdersController@getOrderHistory'));
    }

    public function getOrderDetail($id = null){

        $numberDayDeleteImage = Setting::mediaidRead('MediaidSettingCompany.numberDayDeleteImage',false);

        $imageCreated = Order::select('created_at')
                                ->where('company_id','=',$this->getCurrentCompany('id'))
                                ->where('orders.alias','=',$id)
                                ->get()->first();

        $datetime1 = new DateTime(date('Y-m-d',time()));
        $datetime2 = new DateTime(date('Y-m-d',strtotime($imageCreated['created_at'])));
        $interval = $datetime2->diff($datetime1)->days;

        $settingDeleteImage = $interval - (int)$numberDayDeleteImage;
        //dd($datetime1,$datetime2,$interval,$settingDeleteImage);

        $orderDetail = order::join('users', 'users.id', '=', 'orders.user_id')
                            ->join('stores','stores.id','=','orders.store_id')
                            ->select('orders.*','stores.name','users.first_name_kana','users.last_name_kana','users.first_name', 'users.last_name', 'users.status as user_status', 'users.alias AS user_alias')
                            ->where('orders.alias','=',$id)
                            ->first();
    //dd($orderDetail);

        $messageOrder = order::join('users', 'users.id', '=', 'orders.user_id')
                             ->join('messages','messages.order_id','=','orders.id')
                             ->leftJoin('staffs','staffs.id','=','messages.created_staff_id')
                             ->select('orders.status as orderStatus','messages.*','staffs.first_name as staffFirstName','staffs.last_name as staffLastName','users.first_name', 'users.last_name', 'users.status as user_status', 'users.alias AS user_alias')
                             ->where('orders.alias','=',$id);

        //dd($messageOrder);
        $paginate = $messageOrder->orderBy('messages.id', 'ASC')->paginate(10);
        //dd($paginate);
        //$messages = Message::render($paginate);

        if(isset($orderDetail) && isset($messageOrder) ){
            return view('company.orders.orderdetail',compact('orderDetail','messageOrder','paginate','settingDeleteImage'));
        }else{
            return redirect()->to(action('Company\OrdersController@getOrderHistory'));
        }
    }

    public function getPhotos($alias = null) {
        $order =  $order = Order::select('orders.*', 'users.first_name', 'users.last_name','users.first_name_kana','users.last_name_kana', 'users.email', 'users.phone_number')
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->where('orders.alias', '=', $alias)
            ->where('orders.company_id', '=', $this->getCurrentCompany('id'))
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

        return view("company.orders.photo", ['photos' => $photos->toArray(), 'order' => $order]);
    }

    private function getOrderData($request) {

        $companyId = $this->getCurrentCompany('id');

        $orderQuery = Order::join('users', 'users.id', '=', 'orders.user_id')
                           ->join('stores','stores.id','=', 'orders.store_id')
            ->select('orders.*','stores.name','users.first_name_kana','users.last_name_kana','users.first_name', 'users.last_name', 'users.status as user_status', 'users.alias AS user_alias')
            ->where('orders.company_id', '=', $companyId);
            //dd($orderQuery);
        if(!empty($request['store_name'])){
            $orderQuery = $orderQuery->search(DB::raw('stores.name'), $request['store_name']);
        }

        if (!empty ($request['order_code'])) {
            $orderQuery = $orderQuery->search('orders.order_code', $request['order_code']);
        }

        if (!empty ($request['username'])) {
            $orderQuery = $orderQuery->searchEncrypted('full_name_kana_index', $request['username']);
        }

        if (@$request['status'] != -1 && empty($request['completed_flag']) && empty($request['status_invalid'])) {
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

        if ($receivedDateStart) {
            $orderQuery = $orderQuery->where('orders.created_at', '>=', $receivedDateStart);
        }

        if ($receivedDateEnd) {
            $orderQuery = $orderQuery->where('orders.created_at', '<=', $receivedDateEnd);
        }

        $paginate = $orderQuery->orderBy('orders.id', 'desc')->paginate(10);
        //dd($paginate);

        $orders = Order::render($paginate);

        return view('company.orders.orderhistory', ['paginate' => $paginate,'search' => $request]);
    }
}