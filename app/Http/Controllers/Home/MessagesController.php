<?php

namespace App\Http\Controllers\Home;

//use App\Models\DebugLog;
use App\Models\Device;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Order;
use App\Models\MessageTemplate;
use DB;
use Illuminate\Support\Facades\Mail;
use Validator;
use App\Models\Setting;
use App\Lib\PushNotification;

class MessagesController extends HomeAppController
{

    /**
     * Messages index page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $currentMsgOrdering = session('MessageOrdering');

        if ($request->input('ordering')) {
            $currentMsgOrdering = $request->input('ordering');
        }

        $storeId = $this->getCurrentStore('id');
        $companyId = $this->getCurrentCompany('id');


        if ($currentMsgOrdering == Message::MSG_ORDERING_BY_LATEST_MSG) {


            $orders = Order::join('users', 'users.id', '=', 'orders.user_id')
                ->leftJoin('messages', 'messages.order_id', '=', 'orders.id')
                ->select(DB::raw("orders.*, CAST(SUBSTRING_INDEX(GROUP_CONCAT(messages.`id` ORDER BY messages.`id` DESC SEPARATOR '|'), '|', 1) AS UNSIGNED) AS `last_message_id`,
                CAST(SUBSTRING_INDEX(GROUP_CONCAT(messages.`id` ORDER BY messages.`id` DESC SEPARATOR '|'), '|', 1) AS UNSIGNED) AS `last_message_id`,
                users.first_name, users.last_name, 'users.first_name_kana','users.last_name_kana', users.alias AS user_alias, orders.alias AS order_alias"))
                ->where('orders.store_id', '=', $storeId)
                ->where('orders.company_id', '=', $companyId)
                ->groupBy('orders.id')
                ->orderBy('last_message_id', 'desc')
                ->paginate(10);

        } else {
            $orders = Order::join('users', 'users.id', '=', 'orders.user_id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.first_name_kana', 'users.last_name_kana', 'users.alias AS user_alias', 'orders.alias AS order_alias')
                ->where('orders.store_id', '=', $storeId)
                ->where('orders.company_id', '=', $companyId)
                ->orderBy('orders.id', 'desc')
                ->paginate(10);
        }

        foreach ($orders as $order) {

            $order->full_name = null;

            if (!empty ($order->first_name) || !empty ($order->last_name)) {
                $order->full_name = decrypt_data($order->first_name) . ' ' . decrypt_data($order->last_name);
            } else if (!empty ($order->first_name_kana) || !empty ($order->last_name_kana)) {
                $order->full_name = decrypt_data($order->first_name_kana) . ' ' . decrypt_data($order->last_name_kana);
            }

        }

        if ($orders->count() == 0) {
            return view('home.messages.empty_order');
        }

        $defaultMessages = MessageTemplate::getDefaultMessagesTemplates($storeId, $companyId);

        $messageTemplates = MessageTemplate::getAllMessagesTemplates($storeId, $companyId);

//        $messageTemplates = DB::select('SELECT * FROM `message_templates` WHERE `store_id`=? AND `company_id`=? AND `status`=?', [$storeId, $companyId, MessageTemplate::STATUS_APPLIED]);

        $staff = $this->getCurrentStaff();


        return view('home.messages.index', [
            'paginate' => $orders,
            'jsonData' => [
                'orders' => $orders->toArray()['data'],
                'getMessageUrl' => action('Home\MessagesController@getMessages'),
                'sendMsgUrl' => action('Home\MessagesController@postSendMessage'),
                'sendMsgMailUrl' => action('Home\MessagesController@postSendMessageMail'),
                'msgUrl' => action('Home\MessagesController@getIndex'),
                'changeMsgOrderingUrl' => action('Home\MessagesController@getChangeMsgOrdering'),
                'currentMsgOrdering' => $currentMsgOrdering,
                'updateSeenMessagesUrl' => action('Home\MessagesController@postUpdateSeenMessages'),
                'defaultMessages' => $defaultMessages,
                'messageTemplates' => $messageTemplates,
                'SentMsgConfirm' => __('SentMsgConfirm'),
                'staff' => ['first_name' => $staff['first_name'], 'last_name' => $staff['last_name']],
                'storeName' => $this->getCurrentStore('name')
            ]
        ]);
    }

    /**
     * Gets order messages
     * @param Request $request
     * @return array
     */
    public function getMessages(Request $request)
    {
        $order = Order::findByAlias($request->input('order_alias'));

        if (empty ($order)) {
            return r_err(['Order not found']);
        }

        $messages = Message::select('messages.id', 'messages.type', 'messages.title', 'messages.content', 'messages.created_at', 'target', 'staffs.first_name', 'staffs.last_name', DB::raw('CONCAT (staffs.first_name, staffs.last_name) AS `full_name`'))
            ->leftJoin('staffs', 'staffs.id', '=', 'messages.created_staff_id')
            ->where('order_id', '=', $order->id)
            ->where('messages.company_id', '=', $this->getCurrentCompany('id'))
            ->where('messages.store_id', '=', $this->getCurrentStore('id'))
            ->where('messages.user_id', '=', $order->user_id)
            ->orderBy('messages.created_at', 'desc')
            ->get();

        return r_ok(['messages' => $messages]);
    }

    /**
     * Sends message via mail
     * @param Request $request
     * @return array
     */
    public function postSendMessageMail(Request $request)
    {
        $type = $request->input('type');
        $newMessageId = $request->input('newMessageId');

        switch($type) {
            case 'received':
                $messageType = MessageTemplate::MSG_TYPE_RECEIVED_NOTIFY;
                break;
            case 'prepared':
                $messageType = MessageTemplate::MSG_TYPE_PREPARED_NOTIFY;
                break;
            default:
                $messageType = MessageTemplate::MSG_TYPE_OTHER_NOTIFY;
        }

        $order = Order::findByAlias($request->input('order_alias'));

        if (empty ($order)) {
            return r_err('Order not found');
        }

        if ($order->status == Order::STATUS_INVALID) {
            return r_err('Can not send message to an invalid order');
        }

        $data = [
            'company_id' => $this->getCurrentCompany('id'),
            'store_id' => $this->getCurrentStore('id'),
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $messageType,
            'target' => Message::TARGET_STORE_TO_USER,
            'created_staff_id' => $this->getCurrentStaff('id')
        ];

        $dataNotifi = Message::where('messages.id', '=', $newMessageId)
            ->join('orders', 'messages.order_id', '=', 'orders.id')
            ->join('users', 'messages.user_id', '=', 'users.id')
            ->join('stores', 'messages.store_id', '=', 'stores.id')
            ->select('messages.order_id', 'messages.target', 'messages.created_at', 'orders.alias', 'messages.store_id',
                'stores.name as store_name')
            ->first();

        $settings = Setting::where('store_id', $data['store_id'])->where('company_id', $this->getCurrentCompany('id'))->whereIn('key', ['acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting'])->lists('value', 'key')->toArray();
        $pushResult = [];
        $pushKey['ios'] = '';
        $pushKey['android'] = '';


        // read android key
        $companyAlias = $this->getCurrentCompany('alias');
        $androidKeyPath = base_path('pushAndroid') . "/" . $companyAlias . ".txt";
        $iosKeyPath = base_path('pushIos') . "/" . $companyAlias . '.pem';
        if (file_exists($iosKeyPath)) {
            $pushKey['ios'] = $iosKeyPath;
        }

        if (file_exists($androidKeyPath)) {
            $androidKey = file_get_contents($androidKeyPath);
            $lists = explode("\n", $androidKey);
            $pushKey['android'] = $lists[0];
        }

        try {
            $msgPayload = array(
                'mtitle' => __('You have new message from DrugOrder'),
                'mdesc' => json_encode(r_ok($dataNotifi)),
                'alias' => Order::where('id', $data['order_id'])->first()->alias,
                'name' => Store::where('id', $data['store_id'])->first()->name,
                'settings' => empty($settings) ? null : $settings
            );

            $deviceTokenAndroid = Device::
            where('user_id', $data['user_id'])
                ->where('device_type', 'android')
                ->where('status', 1)
                ->whereNotNull('notification_token')
                ->groupBy('notification_token')
                ->lists('notification_token')
                ->toArray();

            $deviceTokenIos = Device::where('user_id', $data['user_id'])
                ->where('device_type', 'ios')
                ->where('status', 1)
                ->whereNotNull('notification_token')
                ->groupBy('notification_token')
                ->lists('notification_token')
                ->toArray();

            $deviceNotLogin = Device::select('device_type', 'notification_token')
                ->where('alias_last_order_not_member', $request['order_alias'])
                ->where('status', 0)
                ->get();
            if (!empty($deviceNotLogin)) {
                $deviceNotLogin = $deviceNotLogin->toArray();
                foreach ($deviceNotLogin as $key => $value) {
                    if ($value['device_type'] == 'android') {
                        $deviceTokenAndroid[] = $value['notification_token'];
                    }
                    if ($value['device_type'] == 'ios') {
                        $deviceTokenIos[] = $value['notification_token'];
                    }
                }
            }

            // For Android
            if (!empty($deviceTokenAndroid)) {
                $deviceTokenAndroid = (array)$deviceTokenAndroid;
                if ($pushKey['android'] != '') {
                    $pushResult[] = PushNotification::android($msgPayload, $deviceTokenAndroid, $pushKey['android']);
                }

            }
            // For ios
            if (!empty($deviceTokenIos)) {
                $deviceTokenIos = (array)$deviceTokenIos;
                if ($pushKey['ios'] != '') {
                    foreach ($deviceTokenIos as $valueToken) {
                        $pushResult[] = PushNotification::iOS($msgPayload, $valueToken, $pushKey['ios']);
                    }
                }
            }

            if (config('app.debug')) {
                $result['pushResult'] = $pushResult;
                $result['deviceTokenAndroid'] = $deviceTokenAndroid;
                $result['deviceTokenIos'] = $deviceTokenIos;
            }


        } catch (\Exception $e) {
//            DebugLog::error($e);
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        // Send email when recived, complete order
        $emailUser = User::where('id', $data['user_id'])->first()->toArray();
        $emailUser['order_type'] = $request['type'];
        $emailUser['company_name'] = $this->getCurrentCompany('name');
        $emailUser['message_flag'] = 'message';
        $emailUser['order_alias'] = Order::where('id', $data['order_id'])->first()->alias;
        $emailUser['store_name'] = Store::where('id', $data['store_id'])->first()->name;

        $settings = Setting::where('store_id', $data['store_id'])->where('company_id', $this->getCurrentCompany('id'))->whereIn('key', ['patientReplySetting'])->lists('value', 'key')->toArray();
        if (!empty($settings['patientReplySetting'])) {
            $emailUser['patientReplySetting'] = $settings['patientReplySetting'] == '' ? 0 : $settings['patientReplySetting'];
        } else {
            $emailUser['patientReplySetting'] = 0;
        }
        try {
            if ($emailUser['status'] != User::STATUS_EXPELLED && $emailUser['status'] != User::STATUS_EXITED) {

                $valid = [
                    'email' => 'email|max:255'
                ];
                $valids = Validator::make(['email' => $emailUser['email']], $valid);
                if (!$valids->fails()) {
                    Mail::send('email.notification', $emailUser, function ($message) use ($emailUser) {
                        $message->from(env('MAIL_USERNAME'), 'スマホ処方めーる');
                        $message->to($emailUser['email'], 'Hello user')->subject('【スマホ処方めーる/' . $emailUser['company_name'] . 'ドラッグ】会員登録');
                    });
                }
            }
        } catch (Exception $e) {
            if (count(Mail::failures()) > 0) {
            }
        };
    }

    /**
     * Sends message
     * @param Request $request
     * @return array
     */
    public function postSendMessage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'order_alias' => 'required'
        ]);

        if ($validator->fails()) {
            return r_err($validator->errors());
        }

        $orderStatus = -1;
        $messageType = MessageTemplate::MSG_TYPE_OTHER_NOTIFY;

        if ($request->input('type') == 'received') {
            $orderStatus = Order::STATUS_RECEIVED_NOTIFIED;
            $messageType = MessageTemplate::MSG_TYPE_RECEIVED_NOTIFY;

        } else if ($request->input('type') == 'prepared') {
            $orderStatus = Order::STATUS_PREPARED_NOTIFIED;
            $messageType = MessageTemplate::MSG_TYPE_PREPARED_NOTIFY;
        }

        $order = Order::findByAlias($request->input('order_alias'));

        if (empty ($order)) {
            return r_err('Order not found');
        }

        if ($order->status == Order::STATUS_INVALID) {
            return r_err('Can not send message to an invalid order');
        }

        $data = [
            'company_id' => $this->getCurrentCompany('id'),
            'store_id' => $this->getCurrentStore('id'),
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $messageType,
            'target' => Message::TARGET_STORE_TO_USER,
            'created_staff_id' => $this->getCurrentStaff('id')
        ];

        $r = DB::transaction(function () use ($data, $orderStatus, $order, $messageType, $request) {
            $now = time();
            $sentTime = date("Y-m-d H:i:s", $now);
            $result['status'] = $orderStatus;
            $result['sent_at'] = $sentTime;


            $newMessage = Message::create($data);
            $newMessage->save();

            if ($orderStatus != -1) {
                $result['$status'] = Order::$statuses[$orderStatus];

                if ($order->completed_flag == 1 && ($messageType == MessageTemplate::MSG_TYPE_RECEIVED_NOTIFY || $messageType == MessageTemplate::MSG_TYPE_PREPARED_NOTIFY)) {
                    return r_err('Can not send message to a completed order!');
                }

                if ($orderStatus == Order::STATUS_PREPARED_NOTIFIED) {
                    if (!empty ($order->sent_prepared_msg_at)) {
                        return r_err([__('SentPreparedMsgError')]);
                    }

                    DB::update('UPDATE `orders` SET `status` =?, `sent_prepared_msg_at`=?, `completed_at`=?, `update_staff_id`=?, `completed_flag`=1,`updated_at`=NOW() WHERE `id` = ?'
                        , [$orderStatus, $sentTime, $sentTime, $this->getCurrentStaff('id'), $data['order_id']]);

                    $result['sent_prepared_msg_at'] = date('m/d', $now) . '<br>' . date('H:i', $now);
                    $result['completed_flag'] = 1;

                } else if ($orderStatus == Order::STATUS_RECEIVED_NOTIFIED) {
                    if (!empty ($order->sent_prepared_msg_at)) {
                        return r_err([__('SentReceivedMsgErrorPreparedMsg')]);
                    }

                    if (!empty ($order->sent_received_msg_at)) {
                        return r_err([__('SentReceivedMsgError')]);
                    }

                    DB::update('UPDATE `orders` SET `status` =?, `update_staff_id`=?, `sent_received_msg_at`=?,`updated_at`=NOW() WHERE `id` = ?'
                        , [$orderStatus, $this->getCurrentStaff('id'), $sentTime, $data['order_id']]);

                    $result['sent_received_msg_at'] = date('m/d', $now) . '<br>' . date('H:i', $now);

                }
            } else {
                DB::update('UPDATE `orders` SET `update_staff_id`=?, `sent_other_msg_at`=?,`updated_at`=NOW() WHERE `id` = ?'
                    , [$this->getCurrentStaff('id'), $sentTime, $data['order_id']]);
            }

            $result['newMessageId'] = $newMessage->id;

            return r_ok($result);
        });


        //        PushNotification
        return $r;
    }


    /**
     * @param Request $request
     * @return redirect()
     */
    public function getChangeMsgOrdering(Request $request)
    {
        $type = $request->input('type');

        if ($type != 1 && $type != 2) {
            $type = 1;
        }

        session(['MessageOrdering' => $type]);
        return redirect()->to(action('Home\MessagesController@getIndex'));
    }

    /**
     * @param Request $request
     */
    public function postCheckNewMessages(Request $request)
    {
        $unreadMessageCount = DB::select('SELECT COUNT(*) `UnreadMessageCount` FROM `messages` WHERE `company_id`=? AND `store_id`=? AND `seen_at` IS NULL AND `target`=?',
            [$this->getCurrentCompany('id'), $this->getCurrentStore('id'), Message::TARGET_USER_TO_STORE]);
        return r_ok($unreadMessageCount{0});
    }

    /**
     * @param Request $request
     */
    public function postUpdateSeenMessages(Request $request)
    {
        $orderId = Order::findByAlias($request->input('order_alias'), 'id');

        $updateResult = DB::update('UPDATE `messages` SET `seen_at`=? WHERE `order_id`=? AND `target`=?', [current_timestamp(), $orderId, Message::TARGET_USER_TO_STORE]);
        return r_ok($updateResult);
    }


}