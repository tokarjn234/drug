<?php


namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\Device;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Photo;
use App\Models\Store;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\JsonResponse;

use Intervention\Image\Facades\Image;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;


class OrdersController extends ApiAppController
{

    /**
     * Send Prescription
     * @uri /api/orders/send-prescription
     * @method POST
     * @param Request $request
     * @return array
     */
    public function postSendPrescription(Request $request)
    {
        $idComp = Company::findByAlias($request->headers->get('company'), 'id');
        DB::beginTransaction();
        $totalOrders = DB::select('SELECT `order_code` FROM `orders` WHERE `company_id`=? AND `order_code` LIKE "%' . date('ymd', time()) . '%" ORDER BY `order_code` DESC LIMIT 1', [$idComp]);
        if (empty($totalOrders)) {
            $totalOrders = 0;
        } else {
            $totalOrders = (int)substr($totalOrders[0]->order_code, 12);
        }

        $totalOrders = $totalOrders + 1;
        $totalOrders = str_pad($totalOrders, 5, '0', STR_PAD_LEFT);
        // Get Settings
        $systemST = $this->getSettingsOrder($request);
        $systemST = $systemST['data'];
        $drugbookUse = $systemST['drugbook_use']['required'] == true ? 'required' : '';
        $drugbrandChange = $systemST['drugbrand_change']['required'] == true ? 'required' : '';
        unset($systemST['drugbook_use']);
        unset($systemST['drugbrand_change']);
        //Check type account
        if ($request->token['user_id'] == 'anonymous') {

            $validate = [
                'email' => 'email|max:255',
                'store_id' => 'required',
            ];


            foreach ($systemST as $key => $value) {
                if ($key == 'first_name_kana' && $value['required'] == true) {
                    $validate['last_name_kana'] = 'required';
                }
                if ($value['required'] == true) {
                    $validate[$key] = isset($validate[$key]) ? $validate[$key] . '|required' : 'required';
                }
            }


            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return r_err($validator->errors());
            }

            $dataUser['email'] = $request['email'];
            $dataUser['phone_number'] = isset($request['phone_number']) ? $request['phone_number'] : null;
            $dataUser['first_name_kana'] = $request['first_name_kana'];
            $dataUser['last_name_kana'] = $request['last_name_kana'];
            $dataUser['status'] = User::STATUS_NON_MEMBERS;
            $dataUser['email_used'] = json_encode([$request['email']]);
            $dataUser['company_id'] = Company::findByAlias($request->headers->get('company'), 'id');
            $user = User::create($dataUser);
            $user->save();

            $userId = $user->id;
            Device::where('id', $request->token['device_id'])->update(['user_id' => $userId]);

            $dataOrder['user_id'] = $userId;
            $dataOrder['company_id'] = Company::findByAlias($request->headers->get('company'), 'id');
            $company_id = str_pad($dataOrder['company_id'], 4, '0', STR_PAD_LEFT);
            $dataOrder['order_code'] = $company_id . '-' . date('ymd', time()) . '-' . $totalOrders;
        } else {
            $arrValidate = [
                'store_id' => 'required',
                'visit_at' => 'required|date_format:Y-m-d H:i:s',
            ];
            $arrValidate['drugbook_use'] = $drugbookUse;
            $arrValidate['drugbrand_change'] = $drugbrandChange;
            $validator = Validator::make($request->all(), $arrValidate);

            if ($validator->fails()) {
                return r_err($validator->errors());
            }

            $user = User::where('alias', $request->token['user_alias'])->where('status', User::STATUS_MEMBERS)->first();

            if (empty($user)) {
                return r_err([], '', -1);
            }

            $dataOrder['user_id'] = $user->id;
            $companyId = str_pad($user->company_id, 4, '0', STR_PAD_LEFT);
            $dataOrder['order_code'] = $companyId . '-' . date('ymd', time()) . '-' . $totalOrders;
            $dataOrder['company_id'] = Company::findByAlias($request->headers->get('company'), 'id');
        }

//        Create Order
        $dataOrder['completed_flag'] = 0;
        $dataOrder['store_id'] = $request['store_id'];
        $dataOrder['visit_at'] = $request['visit_at'];
        $dataOrder['visit_at_string'] = $request['visit_at_string'];
        $dataOrder['drugbook_use'] = $request['drugbook_use'];
        $dataOrder['drugbrand_change'] = $request['drugbrand_change'];
        $dataOrder['comment'] = $request['comment'];
        $dataOrder['status'] = 0;
        $dataOrder['created_at'] = date('Y-m-d H:i:s', time());
        $orderId = Order::insertGetId($dataOrder);
//        ReUpdate last_order_id in table User
        User::where('id', $dataOrder['user_id'])->update(['last_order_id' => $orderId, 'last_order_at' => $dataOrder['created_at']]);
        $orderAlias = Order::where('id', $orderId)->firstOrFail()->alias;
//        Save last Order alias in table Device if it is anonymous
        if ($request->token['user_id'] == 'anonymous') {
            Device::where('id', $request->token['device_id'])->update(['alias_last_order_not_member' => $orderAlias]);
        }
//        Save Order in orders_transaction
        $dataOrderTransaction['id'] = $orderId;
        $dataOrderTransaction['company_id'] = $dataOrder['company_id'];
        $dataOrderTransaction['order_code'] = $dataOrder['order_code'];
        $dataOrderTransaction['store_id'] = $dataOrder['store_id'];
        $dataOrderTransaction['status'] = $dataOrder['status'];
        $dataOrderTransaction['created_at'] = $dataOrder['created_at'];
        OrderTransaction::insert($dataOrderTransaction);
//        Save image
        $dataPhoto['order_id'] = $orderId;
        $images = $request->file('image');

        $year = date('Y', time());
        $month = date('m', time());
        $companyAlias = $request->headers->get('company');
        if (!empty($images)) {

            if (!is_dir(public_path() . '/images/prescription/' . $companyAlias . '/' . $year . '/' . $month)) {
                mkdir(public_path() . '/images/prescription/' . $companyAlias . '/' . $year . '/' . $month, 0777, true);
            }

            foreach ($images as $value) {

                $filename = uniqid() . '-' . $value->getClientOriginalName();
                $path = public_path('images/prescription/' . $companyAlias . '/' . $year . '/' . $month . '/' . $filename);
                $dataPhoto['photo_url'] = 'images/prescription/' . $companyAlias . '/' . $year . '/' . $month . '/' . $filename;
                $dataPhoto['file_size'] = $value->getClientSize();
                $dataPhoto['file_type'] = $value->getClientOriginalExtension();
                $dataPhoto['status'] = 0;

                if (move_uploaded_file($value->getRealPath(), $path)) {
                    $photoId = Photo::insertGetId($dataPhoto);
                    $photo = Photo::find($photoId);

                    if (!$photo->toEncryptImage()) {
                        DB::rollBack();
                        return r_err([], __('Could not move upload file'));
                    }

                } else {
                    DB::rollBack();
                    return r_err([], __('Could not move upload file'));
                }


            }
        } else {
            DB::rollBack();
            return r_err([], __('No image selected'));
        }
        DB::commit();
        return r_ok(['order_alias' => $orderAlias, 'user_id' => $dataOrder['user_id']], __('Successfull'));
    }

    /**
     * Get list Order history
     * @uri /api/orders/order-history
     * @method GET
     * @param Request $request
     * @return array
     */

    public function getOrderHistory(Request $request)
    {

        $limit = $request['limit'];

        ($limit <= 0 || empty($limit)) ? $limit = 10 : $limit;

        if ($request->token['user_id'] == 'anonymous') {
            return r_ok([], 'You are anonymous');
        }

        $user = User::where('alias', $request->token['user_alias'])->where('status', User::STATUS_MEMBERS)->first();

        if (empty($user)) {
            return r_err([], '', -1);
        }

        $listOrder = Order::
        select('orders.store_id')
            ->where('orders.user_id', $user->id)
            ->groupBy('orders.store_id')
            ->paginate($limit)
            ->toArray();

        if (!empty($listOrder['data'])) {
            foreach ($listOrder['data'] as $key => $value) {
                //Add order detail
                $listOrder['data'][$key] = Order::where('store_id', $value['store_id'])->orderBy('created_at', 'DESC')->where('orders.user_id', $user->id)->first()->toArray();
                // Add store detail
                $store = Store::where('id', $value['store_id'])->first();
                $listOrder['data'][$key]['store'] = empty($store) ? null : $store;
                //Add settings store
                if (!empty($store)) {
                    $settings = Setting::where('store_id', $store->id)->whereIn('key', ['acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting'])->lists('value', 'key')->toArray();

                    if (empty($settings)) {
                        $settings['acceptOrderOnNonBusinessHour'] = 1;
                        $settings['showAlertAtNight'] = 1;
                        $settings['patientReplySetting'] = 1;
                    }
                    $listOrder['data'][$key]['store']['settings'] = $settings;
                }
            }
        }

        return empty($listOrder['data']) ? r_ok([], 'No data found') : r_ok($listOrder);
    }


    // Get Settings Order

    private static function getSettingsOrder(Request $request)
    {
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');
        $registerSettings = Setting::whereCompanyId($companyId)->whereName('CompanyRegisterSetting')->where('key', 'user_input')->get()->lists('value', 'key')->toArray();

        if (empty ($registerSettings)) {
            $userRegisterSetting = User::$defaultRegisterSetting;
        } else {
            $userRegisterSetting = json_decode($registerSettings['user_input'], true);
        }

        $fields = [
            'first_name_kana' => $userRegisterSetting['first_name_kana'],
            'phone_number' => $userRegisterSetting['phone_number'],
            'email' => $userRegisterSetting['email'],
            'drugbook_use' => $userRegisterSetting['drugbook_use'],
            'drugbrand_change' => $userRegisterSetting['drugbrand_change']
        ];


        return r_ok($fields, 'Success');
    }

}