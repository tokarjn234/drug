<?php

namespace App\Http\Controllers\Api;

use App\Models\Device;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\JsonResponse;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class MessagesController extends ApiAppController
{

    /**
     * List Store & Prescription
     * @uri /api/messages/list-store-prescription
     * @method GET
     * @param Request $request
     * @return array
     */

    public function getListStorePrescription(Request $request)
    {
        if ($request->token['user_id'] == 'anonymous') {
            $device = Device::where('id', $request->token['device_id'])->first();
            $orderAlias = $device->alias_last_order_not_member;
            $user = User::where('id', $device->user_id)->first();

//            if ($user->status == User::STATUS_MEMBERS) {
//                return r_ok([], __('Not exits Prescription'));
//            }

            $listPrescription = Order::select('orders.id', 'orders.alias', 'stores.name as name', 'stores.address as address',
                'stores.id as store_id',
                'stores.city1 as city1',
                'stores.province as province')
                ->join('stores', 'orders.store_id', '=', 'stores.id')
//                ->where('orders.user_id', $user->id)
                ->where('orders.alias', $orderAlias)
                ->whereHas('message', function ($query) {
                })
                ->orderBy('orders.created_at', 'DESC')->limit(1)->get()->toArray();

        } else {
            $user = User::where('alias', $request->token['user_alias'])->where('status', User::STATUS_MEMBERS)->first();

            if (empty($user)) {
                return r_err([], '', -1);
            }

            $listPrescription = Order::
            select('orders.id', 'orders.alias', 'stores.name as name', 'stores.address as address',
                'stores.id as store_id',
                'stores.city1 as city1',
                'stores.province as province')
                ->where('orders.user_id', $user->id)
                ->whereHas('message', function ($query) {
                })
                ->orderBy('orders.created_at', 'desc')
                ->join('stores', 'orders.store_id', '=', 'stores.id')
                ->get()->toArray();
        }

        if (empty($listPrescription)) {
            return r_err([], __('Not exits Prescription'));
        }

        $dataOrder = $listPrescription;
        foreach ($listPrescription as $key => $value) {
            $settings = Setting::where('store_id', $value['store_id'])->whereIn('key', ['acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting'])->lists('value', 'key')->toArray();
            if (empty($settings)) {
                $settings['acceptOrderOnNonBusinessHour'] = 1;
                $settings['showAlertAtNight'] = 1;
                $settings['patientReplySetting'] = 1;
            }
            $dataOrder[$key]['settings'] = $settings;
            $listMessUnread = Message::where('order_id', $value['id'])
                ->whereIn('target', [Message::TARGET_SYSTEM_TO_ALL_USERS, Message::TARGET_STORE_TO_ALL_USERS, Message::TARGET_STORE_TO_USER])
                ->where('seen_at', null)
                ->orderBy('created_at')
                ->get()->toArray();
            $dataOrder[$key]['number_mess_unread'] = count($listMessUnread);
            if (!empty($listMessUnread)) {
                $dataOrder[$key]['message_content'] = $listMessUnread[0];
            } else {
                $mess = Message::where('order_id', $value['id'])->orderBy('created_at', 'DESC')->first();
                $dataOrder[$key]['message_content'] = empty($mess) ? null : $mess->toArray();
            }

        }

        return !empty($dataOrder) ? r_ok($dataOrder) : r_ok([], __('Not exits Prescription'));
    }

    /**
     * List Message
     * @uri /api/messages/list-message
     * @method GET
     * @param Request $request
     * @return array
     */

    public function getListMessage(Request $request)
    {
        $timeNow = date('Y-m-d H:i:s', time());
        $orderAlias = $request['alias'];
        if ($request->token['user_id'] == 'anonymous') {
            $orderAlias = Device::where('id', $request->token['device_id'])->first()->alias_last_order_not_member;
        }
        $orderId = Order::findByAlias($orderAlias, 'id');

        $listMessage = Message::where('messages.order_id', $orderId)
            ->join('orders', 'messages.order_id', '=', 'orders.id')
            ->join('users', 'messages.user_id', '=', 'users.id')
            ->select('messages.order_id', 'messages.title', 'messages.content', 'messages.target', 'messages.created_at', 'orders.alias', 'messages.store_id',
                'users.first_name', 'users.last_name', 'users.first_name_kana', 'users.last_name_kana')
//            ->where('messages.order_id', $orderId)
            ->whereIn('target', [Message::TARGET_USER_TO_STORE, Message::TARGET_STORE_TO_USER, Message::TARGET_STORE_TO_ALL_USERS, Message::TARGET_SYSTEM_TO_ALL_USERS])
            ->orderBy('created_at', 'asc')
            ->get();
        Message::where('order_id', $orderId)
            ->whereIn('target', [Message::TARGET_STORE_TO_USER, Message::TARGET_SYSTEM_TO_ALL_USERS, Message::TARGET_STORE_TO_ALL_USERS])
//            ->where('user_id', $user->id)
            ->update(['seen_at' => $timeNow]);

        return !empty($listMessage) ? r_ok($listMessage->toArray()) : r_ok([], __('No data found'));

    }

    /**
     * Post Update Read Message
     * @uri /api/messages/read-message
     * @method POST
     * @param Request $request
     * @return array
     */

    public function postReadMessage(Request $request)
    {
        $timeNow = date('Y-m-d H:i:s', time());
        $orderAlias = $request['order_alias'];
        $orderId = Order::findByAlias($orderAlias, 'id');
        $success = Message::where('order_id', $orderId)
            ->whereIn('target', [Message::TARGET_STORE_TO_USER, Message::TARGET_SYSTEM_TO_ALL_USERS, Message::TARGET_STORE_TO_ALL_USERS])
            ->update(['seen_at' => $timeNow]);
        return $success > 0 ? r_ok([], __('Sucessfull')) : r_err([], 'Update fail');
    }

    /**
     * Post Send Message
     * @uri /api/messages/send-message
     * @method POST
     * @param Request $request
     * @return array
     */

    public function postSendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return r_err($validator->errors());
        }


        $orderAlias = $request['alias'];

        $dataMessage['user_id'] = Order::findByAlias($orderAlias, 'user_id');
        $dataMessage['store_id'] = Order::findByAlias($orderAlias, 'store_id');
        $dataMessage['order_id'] = Order::findByAlias($orderAlias, 'id');
        $dataMessage['company_id'] = Order::findByAlias($orderAlias, 'company_id');
        $dataMessage['template_id'] = $request['template_id'];
        $dataMessage['type'] = 1;
        $dataMessage['title'] = $request['title'];
        $dataMessage['content'] = $request['content'];
        $dataMessage['target'] = Message::TARGET_USER_TO_STORE;

        $message = Message::create($dataMessage);


        if ($message->save()) {
            return r_ok([$dataMessage], __('Successfully'));
        } else {
            return r_err([$dataMessage], 'Send message fail');
        }

    }

    /**
     * Update status Message
     * @uri /api/messages/update-message
     * @method PUT
     * @param Request $request
     * @return array
     */

    public function putUpdateMessage(Request $request)
    {

        $messageId = $request['id'];
        $message = Message::where('id', $messageId)->first()->toArray();
        if (empty($message)) {
            return r_err([$request->all()], ['Message not exist']);
        }
        Message::where('id', $messageId)->update(['seen_at' => date('Y-m-d H:i:s')]);
        return r_ok([$request->all()], 'Update successfully');
    }

    /**
     * Get Number Message Unread
     * @uri /api/messages/message-unread
     * @method GET
     * @param Request $request
     * @return array
     */

    public function getMessageUnread(Request $request)
    {
        $user = User::where('alias', $request->token['user_alias'])->where('status', User::STATUS_MEMBERS)->first();

        if (empty($user) || $request->token['user_id'] == 'anonymous') {
            $device = Device::where('id', $request->token['device_id'])->first();
            if (!empty($device->alias_last_order_not_member)) {
                $orderId = Order::findByAlias($device->alias_last_order_not_member, 'id');
                $numberMessage = count(Message::where('order_id', $orderId)
                    ->whereIn('target', [Message::TARGET_STORE_TO_USER, Message::TARGET_STORE_TO_ALL_USERS, Message::TARGET_SYSTEM_TO_ALL_USERS])
                    ->where('seen_at', null)->get());
            } else {
                $numberMessage = 0;
            }
        } else {
            $numberMessage = count(Message::where('user_id', $user->id)
                ->whereIn('target', [Message::TARGET_STORE_TO_USER, Message::TARGET_STORE_TO_ALL_USERS, Message::TARGET_SYSTEM_TO_ALL_USERS])
                ->where('seen_at', null)->get());
        }
        return r_ok($numberMessage, 'Successfully');
    }

    /**
     * update Notification Token
     * @uri /api/messages/update-notification-token
     * @method POST
     * @param Request $request
     * @return array
     */

    public function postUpdateNotificationToken(Request $request)
    {
        $deviceCode = $request['device_code'];
        $userId = $request['user_id'];
        $notificationToken = $request['notification_token'];
        $device = Device::where('user_id', $userId)->where('device_code', $deviceCode)->first();
        if (!empty($device)) {
            Device::where('id', $device->id)->update(['notification_token' => $notificationToken]);
            return r_ok([], 'Successfully');
        } else {
            return r_err([], __('Data not found'));
        }
    }
}