<?php


namespace App\Http\Controllers\Company;

use App\Http\Controllers\Api\SystemControllers;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;
//use Request;
use App\Http\Requests;
use App\Models\User;
use App\Models\Setting;
use Validator, Session, Redirect, DB, Auth;

class SettingUsersController extends CompanyAppController
{
    /**
     * Users settings page
     * @return View
     */
    public function getIndex()
    {


        //$st = Setting::getSettingsRegister($this->getCurrentCompany('id'), 'CompanyRegisterSetting', $registerFields);

        $userRegisterSetting = Setting::companyRead('CompanyRegisterSetting.user_input');

        $userRegisterSetting = json_decode($userRegisterSetting, true);

        if (empty ($userRegisterSetting)) {
            $userRegisterSetting = (object)User::$defaultRegisterSetting;
        } else {
            $userRegisterSetting = (object)$userRegisterSetting;
        }

        $acceptOrderOnNonBusinessHour = Setting::companyRead('StoreTimeSetting.acceptOrderOnNonBusinessHour', false);
        $showAlertAtNight = Setting::companyRead('StoreTimeSetting.showAlertAtNight', false);
        $patientReplySetting = Setting::companyRead('StoreTimeSetting.patientReplySetting', false);
        $settingChangeOnStoreHour = Setting::companyRead('StoreTimeSetting.settingChangeOnStoreHour', false);
        $settingChangeOnStoreAtNight = Setting::companyRead('StoreTimeSetting.settingChangeOnStoreAtNight', false);
        $settingChangeOnStorePatientReply = Setting::companyRead('StoreTimeSetting.settingChangeOnStorePatientReply', false);
        $settingStoreLocalScreenDisplay = Setting::companyRead('StoreTimeSetting.settingStoreLocalScreenDisplay', false);
        $autoLogin = Setting::companyRead('CompanySettingAutoLogin.loginAuto', false);
        $settingAutoLogin = json_decode($autoLogin, true);
        $patientReplySettingMediaid = Setting::companyRead('MediaidSettingCompany.patientReplySettingMediaid', '{"used":0,"billable":"1"}');
        $patientReplySettingMediaid = json_decode($patientReplySettingMediaid, true);

        if (isset($settingAutoLogin['auto'])) {
            $settingAutoLogin = $settingAutoLogin['auto'];
        } else {
            $settingAutoLogin = false;
        }

        return view('company.settingUser.index', compact('acceptOrderOnNonBusinessHour',
            'showAlertAtNight', 'patientReplySetting', 'settingChangeOnStoreHour',
            'settingChangeOnStoreAtNight', 'settingChangeOnStorePatientReply', 'settingAutoLogin',
            'settingStoreLocalScreenDisplay', 'userRegisterSetting', 'patientReplySettingMediaid'));
    }

    public function postSetting(Request $request)
    {

        Setting::companyWrite('StoreTimeSetting.acceptOrderOnNonBusinessHour', $request->input('acceptOrderOnNonBusinessHour'));
        Setting::companyWrite('StoreTimeSetting.showAlertAtNight', $request->input('showAlertAtNight'));
        Setting::companyWrite('StoreTimeSetting.patientReplySetting', $request->input('patientReplySetting'));
        Setting::companyWrite('StoreTimeSetting.settingChangeOnStoreHour', $request->input('settingChangeOnStoreHour'));
        Setting::companyWrite('StoreTimeSetting.settingChangeOnStoreAtNight', $request->input('settingChangeOnStoreAtNight'));
        Setting::companyWrite('StoreTimeSetting.settingChangeOnStorePatientReply', $request->input('settingChangeOnStorePatientReply'));

        $requestAutoLogin = $request->input('settingAutoLogin');

        if ($requestAutoLogin) {
            if ($requestAutoLogin == '1') {
                $settingAutoLogin ['auto'] = true;
            } else {
                $settingAutoLogin ['auto'] = false;
            }
        }

        $settingAutoLogin ['minutes'] = 15;

        Setting::companyWrite('CompanySettingAutoLogin.loginAuto', json_encode($settingAutoLogin));

        return redirect()->to(action('Company\SettingUsersController@getIndex'));
    }

    public function postSettingStoreLocal(Request $request)
    {

        Setting::companyWrite('StoreTimeSetting.settingStoreLocalScreenDisplay', $request->input('settingStoreLocalScreenDisplay'));

        return redirect()->to(action('Company\SettingUsersController@getIndex'));
    }

    /**
     * Saves user input settings
     * @param Request $request
     * @return mixed
     */
    public function postSettingRegisterUser(Request $request)
    {
        $data = $request->all();

        $settings = Setting::companyRead('CompanyRegisterSetting.user_input');

        $settings = json_decode($settings, true);

        if (empty ($settings)) {
            $settings = User::$defaultRegisterSetting;
        }

        foreach ($data['display'] as $k => $v) {
            $settings[$k]['display'] = !!$v;
            $settings[$k]['required'] = $settings[$k]['display'] == 0 ? false : !!$data['required'][$k];
        }


        Setting::companyWrite('CompanyRegisterSetting.user_input', $settings);

        return redirect()->to(action('Company\SettingUsersController@getIndex'));
    }


}