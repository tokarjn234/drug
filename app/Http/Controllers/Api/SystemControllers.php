<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use Validator;

use App\Models\Setting;
use App\Models\Company;
use App\Models\Store;
use App\Models\User;

class SystemControllers extends ApiAppController
{

    /**
     * Global Settings
     * @uri /api/system/global-settings
     * @method GET
     * @param Request $request
     * @return array
     */
    public function getGlobalSettings(Request $request)
    {
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');

        $registerFields['user_input'] = User::$defaultRegisterSetting;

        $registerSettings = Setting::whereCompanyId($companyId)->whereName('CompanyRegisterSetting')->whereIn('key', array_keys($registerFields))->get()->lists('value', 'key');

        if (empty ($registerSettings)) {
            $registerSettings = [];
        }

        foreach ($registerFields as $field => $value) {
            if (isset($registerSettings[$field])) {
                $registerSettings[$field] = json_decode($registerSettings[$field], true);
            } else {
                $registerSettings[$field] = $value;
            }
        }
        // Get Setting login retry count
        $loginRetry['login_retry'] = [
            "count" => 10,
            "minutes" => 5
        ];

        $numberLogin = Setting::whereCompanyId($companyId)->whereName('CompanyRegisterSetting')->whereIn('key', array_keys($loginRetry))->get()->lists('value', 'key');
        // Get Setting auto login
        $loginAuto = Setting::whereCompanyId($companyId)->whereName('CompanySettingAutoLogin')->where('key', 'loginAuto')->get()->lists('value', 'key');
        $dataLoginAutoDefault = ['auto' => true, 'minutes' => 10];
        if (empty($loginAuto['loginAuto'])) {
            Setting::create(['name' => 'CompanySettingAutoLogin', 'key' => 'loginAuto', 'value' => json_encode($dataLoginAutoDefault), 'company_id' => $companyId]);
        }


        $setting = Setting::whereCompanyId($companyId)->whereName('CompanyStoreSetting')->whereKey('store_info_input')->first();

        $storeSettings = Store::$defaultInputSetting;

        if (!empty ($setting)) {
            $storeSettings = json_decode($setting->value, true);
        }

        //Setting search via Name
        $settingSearchViaName = Setting::whereCompanyId($companyId)->whereName('StoreTimeSetting')->where('key', 'settingStoreLocalScreenDisplay')->lists('value', 'key')->toArray();

        if (empty($settingSearchViaName)) {
            $dataCreate = ['name' => 'StoreTimeSetting', 'key' => 'settingStoreLocalScreenDisplay', 'value' => 1, 'company_id' => $companyId];
            Setting::create($dataCreate);
            $storeSettings['settingSearchViaName'] = true;
        } else {
            $storeSettings['settingSearchViaName'] = $settingSearchViaName['settingStoreLocalScreenDisplay'] == 0 ? false : true;
        }


        // Return result
        $result = [
            'registerSettings' => $registerSettings,
            'loginRetry' => !empty($numberLogin['login_retry']) ? json_decode($numberLogin['login_retry']) : $loginRetry['login_retry'],
            'storeSetting' => $storeSettings,
            'loginAuto' => isset($loginAuto['loginAuto']) ? json_decode($loginAuto['loginAuto']) : $dataLoginAutoDefault
        ];

        return r_ok($result, 'Success');
    }

}