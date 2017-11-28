<?php

namespace App\Http\Controllers\WinApi;

use App\Http\Controllers\AppController;
use App\Models\AccessTokenWinApp;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Photo;
use App\Models\Setting;
use Auth;
use DB;
use App\Models\Company;
use App\Models\Staff;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends AppController
{
    public function postLogin(Request $request)
    {
        if (empty($_SERVER['SSL_CLIENT_S_DN_CN'])) {
            return r_err([], ['Your certificate is invalid or expired']);
        } else {
            $cert = Certificate::where('ssl_client_s_dn_cn', '=', $_SERVER['SSL_CLIENT_S_DN_CN'])->first();
        }

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'username' => 'required|max:255',
                'password' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return r_err($validator->errors(), ['']);
            }

            $loginData = array(
                'id' => $request->input('username'),
                'password' => $request->input('password'),
                'company_id' => $cert->company_id
            );

            if (!has_valid_cert_win_app()) {
                return r_err([], ['You must instal certificate first']);
            }
            $sslClientSDnCn = $_SERVER['SSL_CLIENT_S_DN_CN'];

            $cert = Certificate::where('ssl_client_s_dn_cn', '=', $sslClientSDnCn)->first();

            if (empty ($cert)) {
                return r_err([], ['Your certificate is invalid or expired']);
            }

            $staff = Staff::where('username', $loginData['id'])->where('company_id', $cert->company_id)->where('account_type', Staff::ACCOUNT_TYPE_STORE)->whereIn('status', [Staff::STATUS_UNREGISTER, Staff::STATUS_REGISTER])->first();
            if (empty($staff)) {
                return r_winapp([], ['Invalid username or password'], 1);
            }
            $loginSuccess = Hash::check($loginData['password'], $staff->password) ? true : false;
            if ($loginSuccess) {
                $company = Company::find($staff->company_id);
                if (empty ($company)) {
                    return r_err([], ['Company not found']);
                }
                $store = Store::find($cert->store_id);

                if (empty ($store)) {
                    return r_err(['ThereIsNoAvailableStore'], ['ThereIsNoAvailableStore']);
                }
                $access['access_token'] = str_random(40);
                $access['store_id'] = $cert->store_id;
                $access['company_id'] = $cert->company_id;
                $access['user_id'] = $staff->email;
                $access['staff_id'] = $staff->username;
                $access['password_login'] = $request->input('password');
                $access['expires'] = date('Y-m-d H:i:s', strtotime('+365 days', time()));
                AccessTokenWinApp::insert($access);
                $dataReturn = array(
                    'company_name' => Company::select('name')->where('id', $cert->company_id)->first()->name,
                    'first_name' => $staff->first_name,
                    'last_name' => $staff->last_name,
                    'first_name_kana' => $staff->first_name_kana,
                    'last_name_kana' => $staff->last_name_kana,
                    'access_token' => $access['access_token'],
                    'store_name' => $store->name,
                    'user_id' => $loginData['id']
                );

                $setting = Setting::where('store_id', $cert->store_id)->whereIn('key', ['settingWinAppTimeInterval'])->lists('value', 'key')->toArray();
                empty($setting) ? $dataReturn['setting_interval'] = 120 : $dataReturn['setting_interval'] = $setting['settingWinAppTimeInterval'];


                return r_ok($dataReturn, ['Login successfull']);
            } else {
                return r_err(['InvalidUsernameOrPassword'], ['InvalidUsernameOrPassword']);
            }
        }
    }

    public function getPrescription(Request $request)
    {
        $accountAccess = AccessTokenWinApp::where('access_token', $request->access_token)->first();
        $storeId = $accountAccess->store_id;
        $order = OrderTransaction::where('order_transaction.store_id', $storeId)
            ->whereNull('order_transaction.print_at')
            ->select('orders.id', 'orders.order_code', 'orders.user_id',
                'users.first_name', 'users.last_name', 'users.first_name_kana', 'users.last_name_kana', 'users.email', 'users.phone_number',
                'orders.visit_at_string as visit_at_string', 'orders.visit_at as visit_at', 'orders.drugbook_use as drugbook_use', 'orders.drugbrand_change', 'orders.comment',
                'orders.status', 'order_transaction.popup_at', 'order_transaction.created_at')
            ->join('orders', 'orders.id', '=', 'order_transaction.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->get()->toArray();
        $orderWithImg = (array)$order;
        foreach ($order as $key => $value) {
            $orderWithImg[$key]['time_convert'] = Order::parseVisitTimeString($value['visit_at_string'], $value['visit_at']);
//            $orderWithImg[$key]['image_prescription'] = Photo::where('order_id', $value['id'])->lists('photo_url')->toArray();
            $orderWithImg[$key]['url_root'] = url();

            $orderWithImg[$key]['first_name'] = decrypt_data($orderWithImg[$key]['first_name']);
            $orderWithImg[$key]['last_name'] = decrypt_data($orderWithImg[$key]['last_name']);
            $orderWithImg[$key]['first_name_kana'] = decrypt_data($orderWithImg[$key]['first_name_kana']);
            $orderWithImg[$key]['last_name_kana'] = decrypt_data($orderWithImg[$key]['last_name_kana']);
            $orderWithImg[$key]['email'] = decrypt_data($orderWithImg[$key]['email']);
            $orderWithImg[$key]['phone_number'] = decrypt_data($orderWithImg[$key]['phone_number']);
            $orderWithImg[$key]['time_convert'] = Order::parseVisitTimeString($value['visit_at_string'], $value['visit_at']);
            $imgs = Photo::where('order_id', $value['id'])->get();
            if (!empty($imgs)) {
                foreach ($imgs as $k => $photo) {
                    $orderWithImg[$key]['image_prescription'][$k] = action('Home\PhotosController@getView', ['alias' => $photo->alias]);
                }
            }
            $orderWithImg[$key]['url_root'] = url();
        }

        if (empty($order)) {
            return r_ok(['No data found'], ['No data found']);
        }
        return r_ok($orderWithImg, ['Successfull']);
    }

    public function getNewPrescription(Request $request)
    {
        $accountAccess = AccessTokenWinApp::where('access_token', $request->access_token)->first();
        $storeId = $accountAccess->store_id;
        $time = strtotime('-2 days', time());
        $order = OrderTransaction::where('order_transaction.store_id', $storeId)
            ->where('orders.created_at', '>=', date('Y-m-d H:i:s', $time))
            ->where('orders.completed_flag', Order::COMPLETE_FLAG_PENDING)
            ->select('orders.id', 'orders.order_code', 'orders.user_id',
                'users.first_name', 'users.last_name', 'users.first_name_kana', 'users.last_name_kana', 'users.email', 'users.phone_number',
                'orders.visit_at_string as visit_at_string', 'orders.drugbook_use as drugbook_use', 'orders.drugbrand_change',
                'orders.status', 'order_transaction.popup_at', 'order_transaction.created_at')
            ->join('orders', 'orders.id', '=', 'order_transaction.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->get()->toArray();
        $orderWithImg = (array)$order;
        foreach ($order as $key => $value) {
            $orderWithImg[$key]['first_name'] = decrypt_data($orderWithImg[$key]['first_name']);
            $orderWithImg[$key]['last_name'] = decrypt_data($orderWithImg[$key]['last_name']);
            $orderWithImg[$key]['first_name_kana'] = decrypt_data($orderWithImg[$key]['first_name_kana']);
            $orderWithImg[$key]['last_name_kana'] = decrypt_data($orderWithImg[$key]['last_name_kana']);
            $orderWithImg[$key]['email'] = decrypt_data($orderWithImg[$key]['email']);
            $orderWithImg[$key]['phone_number'] = decrypt_data($orderWithImg[$key]['phone_number']);
            $orderWithImg[$key]['time_convert'] = Order::parseVisitTimeString($value['visit_at_string'], $value['visit_at']);
            $imgs = Photo::where('order_id', $value['id'])->get();
            if (!empty($imgs)) {
                foreach ($imgs as $k => $photo) {
                    $orderWithImg[$key]['image_prescription'][$k] = action('Home\PhotosController@getView', ['alias' => $photo->alias]);
                }
            }
            $orderWithImg[$key]['url_root'] = url();
        }

        if (empty($order)) {
            return r_ok(['No data found'], ['No data found']);
        }
        return r_ok($orderWithImg, ['Successfull']);
    }

    public function postUpdatePrintPrescription(Request $request)
    {
        $orderCode = $request['order_code'];
        $order = OrderTransaction::where('order_code', $orderCode)->first();
        if (!empty($order)) {
            OrderTransaction::where('order_code', $orderCode)->update(['print_at' => date('Y-m-d H:i:s', time())]);
            return r_ok([], ['Successfull']);
        } else {
            return r_err([], ['No data found']);
        }
    }

    public function getIntervalSetting(Request $request)
    {
        $accountAccess = AccessTokenWinApp::where('access_token', $request->access_token)->first();

        $setting = Setting::where('store_id', $accountAccess->store_id)->whereIn('key', ['settingWinAppTimeInterval'])->lists('value', 'key')->toArray();
        return empty($setting) ? r_winapp(['settingWinAppTimeInterval' => 120], ['No setting']) : r_winapp($setting);
    }

    public function postLogout(Request $request)
    {
        $result = AccessTokenWinApp::where('access_token', $request->access_token)->update(['access_token' => null]);
        return $result > 0 ? r_winapp([], ['Successfull']) : r_winapp([], ['Error'], 1);
    }

}