<?php

namespace App\Http\Controllers\Mediaid;


use App\Models\Certificate;
use App\Models\Company;
use App\Models\MetaCompany;
use App\Models\PasswordStaff;
use App\Models\Staff;
use Illuminate\Http\Request;
use DB;
use App\Models\Store;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class CompaniesController extends MediaidAppController
{
    public function getIndex(Request $request)
    {
        $action = $request['act'];
        if ($action != 'search') {
            $request->session()->forget('valueSearch');
        }
        $limit = 10;
        $search = session('valueSearch');
        if (empty($search)) {
            $search = [
                'company_name' => '',
                'show_all_cpn' => ''
            ];
        }
        $companyName = $search['company_name'];
        $showAll = $search['show_all_cpn'];
        $status = array(
            'STATUS_PREPARE' => Company::STATUS_PREPARE,
            'STATUS_IN_USE' => Company::STATUS_IN_USE,
            'STATUS_CANCELLATION_COMPLETED' => Company::STATUS_CANCELLATION_COMPLETED,
        );
        if ($showAll) {
            $status['STATUS_CANCELLATION_COMPLETED'] = Company::STATUS_CANCELLATION_COMPLETED;
        } else {
            unset($status['STATUS_CANCELLATION_COMPLETED']);
        }
        $paginate = Company::whereIn('status', $status)->search('name', $companyName, ' ')->with('meta_company')->orderBy('id', 'DESC')->paginate($limit);
        if ($action == 'search') {
            $paginate->appends(['act' => 'search']);
        }

        $companies = Company::render($paginate);
        // -----------------------

        $valueSearch = array(
            'company_name' => $search['company_name'],
            'show_all_cpn' => $search['show_all_cpn'],
        );

        $request->session()->put(['valueSearch' => $valueSearch]);

        return view('mediaid.companies.index')->with(['companies' => $companies, 'paginate' => $paginate, 'valueSearch' => $valueSearch]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex(Request $request)
    {
        $valueSearch = array(
            'company_name' => $request['company_name'],
            'show_all_cpn' => $request['show_all_cpn'],
        );
        $request->session()->put(['valueSearch' => $valueSearch]);
        return redirect()->to(action('Mediaid\CompaniesController@getIndex') . '?act=search');
    }

    public function getDetail(Request $request)
    {
        $alias = $request['id'];
        $company = Company::where('alias', $alias)->with('meta_company')->first()->toArray();
        if (empty($company)) {
            return view('errors.404');
        }
        if (!empty($company['billable'])) {
            $company['billable'] = json_decode($company['billable'], true);
        } else {
            $company['billable'] = ['billable' => 2, 'text' => ''];
        }
        $company['cert_add'] = json_decode($company['cert_add'], true);
        $company['staff_add'] = json_decode($company['staff_add'], true);
        // Return All Store public
        $store = Store::where('company_id', $company['id'])->where('is_published', 1)->whereNull('is_deleted')->get()->toArray();

        $staff['basicStaffPerStore'] = Setting::mediaidRead('MediaidSettingCompany.basicStaffPerStore', 0, $company['id']);
        $staff['numberCertificatePerStore'] = Setting::mediaidRead('MediaidSettingCompany.numberCertificatePerStore', 0, $company['id']);
        $staff['numberDayDeleteImage'] = Setting::mediaidRead('MediaidSettingCompany.numberDayDeleteImage', 0, $company['id']);

        $staff['deletedStaff'] = count(Staff::where('company_id', $company['id'])->where('account_type', Staff::ACCOUNT_TYPE_STORE)->whereIn('status', [Staff::STATUS_DELETED])->get());
        $staff['usedStaff'] = count(Staff::where('company_id', $company['id'])->where('account_type', Staff::ACCOUNT_TYPE_STORE)->whereIn('status', [Staff::STATUS_REGISTER, Staff::STATUS_UNREGISTER, Staff::STATUS_LOCKOUT, Staff::STATUS_ACCOUNT_LOCK])->get());
        $staff['freeStaff'] = $staff['basicStaffPerStore'] * $company['contract_store'] + $company['staff_add']['number'] - $staff['usedStaff'];

        $settings = Setting::where('company_id', $company['id'])->where('name', 'MediaidSettingCompany')->lists('value', 'key')->toArray();
        $settingsRender = [];
        foreach ($settings as $k => $v) {
            $settings[$k] = json_decode($v, true);
            $settingsRender[$k]['used'] = $settings[$k]['used'] == 1 ? '利用する' : '利用しない';
            $settingsRender[$k]['billable'] = $settings[$k]['billable'] == 1 ? '※課金対象' : '';
        }
        //Cert
        $cert['totalCerCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->count();
        $cert['dividedToDeviceCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->whereStatus(Certificate::STATUS_DIVIDED_TO_DEVICE)->count();
        $cert['availableCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->count();
        $cert['inactiveCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->whereStatus(Certificate::STATUS_INACTIVE)->count();
        $cert['dividedToStoreCount'] = $cert['totalCerCount'] - $cert['availableCount'];

        return view('mediaid.companies.detail')->with('company', $company)->with(['storePublic' => $store, 'staff' => $staff, 'setting' => $settingsRender, 'cert' => $cert]);
    }

    public function getCreate(Request $request)
    {
        //Cert
        $cert['numberCertificateAvailable'] = count(Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereNull('company_id')->lists('ssl_client_s_dn_cn', 'id'));
        return view('mediaid.companies.create')->with(['cert' => $cert]);
    }

    public function postCreate(Request $request)
    {
        DB::beginTransaction();
        //Validate
        if ($request['status'] == Company::STATUS_IN_USE) {
            $validator = [
                'name' => 'required',
                'name_manager' => 'required',
                'phone_number' => 'required',
                'fax' => 'required',
                'postal_code_headquarters.0' => 'required',
                'postal_code_headquarters.1' => 'required',
                'headquarters' => 'required',
                'bill_to_postal_code.0' => 'required',
                'bill_to_postal_code.1' => 'required',
                'bill_to_address' => 'required',
                'bill_to_destination' => 'required',
                'contract_store' => 'required',
                'staff_add.number' => 'required',
                'billable.billable' => 'required',
                'billable.text' => 'required',
                'cert_add.number' => 'required',
                'basicStaffPerStore' => 'required',
                'numberCertificatePerStore' => 'required',
                'numberDayDeleteImage' => 'required',
                'staff.username' => 'required',
                'staff.department' => 'required',
                'staff.first_name' => 'required',
                'staff.last_name' => 'required',
                'staff.first_name_kana' => 'required',
                'staff.last_name_kana' => 'required',
            ];
            $validate = Validator::make($request->all(), $validator);
            if ($validate->fails()) {
                return redirect()->to(action('Mediaid\CompaniesController@getCreate'))
                    ->withErrors(['certError' => __('Cannot create company.')])
                    ->withInput();
            }
        }

        $dataCompany = [
            'name' => $request['name'],
            'name_manager' => $request['name_manager'],
            'phone_number' => $request['phone_number'],
            'fax' => $request['fax'],
            'status' => $request['status'],
            'postal_code_headquarters' => implode('-', $request['postal_code_headquarters']),
            'headquarters' => $request['headquarters'],
            'bill_to_postal_code' => implode('-', $request['bill_to_postal_code']),
            'bill_to_address' => $request['bill_to_address'],
            'bill_to_destination' => $request['bill_to_destination'],
            'contract_store' => $request['contract_store'],
            'staff_add' => json_encode($request['staff_add']),
            'billable' => json_encode($request['billable']),
            'cert_add' => json_encode($request['cert_add'])

        ];
        $company = Company::create($dataCompany);

        $dataMetaCompany = $request['meta_company'];
        $dataMetaCompany['company_id'] = $company->id;
        $metaCompany = MetaCompany::create($dataMetaCompany);
        $company->meta_company_id = $metaCompany->id;
        $company->save();


        $pass = $this->get_random_string(7);

        $dataUser = $request['staff'];
        $dataUser['company_id'] = $company->id;
        $dataUser['account_type'] = Staff::ACCOUNT_TYPE_COMPANY;
        $dataUser['must_change_password'] = 1;
        $dataUser['password'] = Hash::make($pass);
        $dataUser['status'] = Staff::STATUS_UNREGISTER;
        $dataUser['last_status'] = $dataUser['status'];
        $dataUser['number_login_retry'] = 5;
        Staff::create($dataUser);

        $settings = [
            'numberCertificatePerStore' => $request['numberCertificatePerStore'],
            'basicStaffPerStore' => $request['basicStaffPerStore'],
            'patientReplySettingMediaid' => json_encode($request['patientReplySettingMediaid']),
            'memberForMessageDeliveryMediaid' => json_encode($request['memberForMessageDeliveryMediaid']),
            'hotlineServiceMediaid' => json_encode($request['hotlineServiceMediaid']),
            'hotline24ServiceMediaid' => json_encode($request['hotline24ServiceMediaid']),
            'numberCertificate' => $request['numberCertificate'],
            'numberDayDeleteImage' => $request['numberDayDeleteImage'],
        ];

        foreach ($settings as $k => $v) {
            Setting::mediaidWrite('MediaidSettingCompany.' . $k, $v, $company->id);
        }

        //Create default settings
        $dataDefaultSetting = [
            'acceptOrderOnNonBusinessHour' => 1,
            'showAlertAtNight' => 1,
            'patientReplySetting' => 1,
            'settingChangeOnStoreHour' => 1,
            'settingChangeOnStoreAtNight' => 1,
            'settingChangeOnStorePatientReply' => 2,
        ];
        foreach ($dataDefaultSetting as $k => $v) {
            Setting::mediaidWrite('StoreTimeSetting.' . $k, $v, $company->id);
        }

        $companyAlias = Company::where('id', $company->id)->first()->alias;
        if ($request['status'] == Company::STATUS_IN_USE) {
            $certAdd = $request['numberCertificatePerStore'] * $request['contract_store'] + $request['cert_add']['number'];

            for ($i = 1; $i <= $certAdd; $i++) {
                $certAvailable = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereNull('company_id')->first();
                if (!empty($certAvailable)) {
                    $update = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->where('id', $certAvailable->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                    if ($update < 0) {
                        DB::rollBack();
                        return redirect()->to(action('Mediaid\CompaniesController@getCreate'))
                            ->withErrors(['certError' => __('Not enough to issue certificates.')])
                            ->withInput();
                    }
                } else {
                    DB::rollBack();
                    return redirect()->to(action('Mediaid\CompaniesController@getCreate'))
                        ->withErrors(['certError' => __('Not enough to issue certificates.')])
                        ->withInput();
                }
            }
            DB::commit();
            return redirect()->action('Mediaid\CompaniesController@getConfirm')->with('company', ['name' => $company->name, 'id' => $company->id])->with('staff', ['username' => $dataUser['username'], 'password' => $pass]);
        }
        DB::commit();

        return Redirect::to('mediaid\companies\detail?id=' . $companyAlias);
    }

    public function getEdit(Request $request)
    {

        $alias = $request['id'];
        $company = Company::where('alias', $alias)->with('meta_company')->first()->toArray();
        if (empty($company)) {
            return view('errors.404');
        }
        if (!empty($company['billable'])) {
            $company['billable'] = json_decode($company['billable'], true);
        } else {
            $company['billable'] = ['billable' => 2, 'text' => ''];
        }
        if (!empty($company['staff_add'])) {
            $company['staff_add'] = json_decode($company['staff_add'], true);
        } else {
            $company['staff_add'] = ['number' => 0, 'text' => ''];
        }
        if (!empty($company['staff_add'])) {
            $company['cert_add'] = json_decode($company['cert_add'], true);
        } else {
            $company['cert_add'] = ['number' => 0, 'text' => ''];
        }
        $store = Store::where('company_id', $company['id'])->where('is_published', 1)->whereNull('is_deleted')->get()->toArray();
        $staff['basicStaffPerStore'] = Setting::mediaidRead('MediaidSettingCompany.basicStaffPerStore', 0, $company['id']);
        $staff['deletedStaff'] = count(Staff::where('company_id', $company['id'])->where('account_type', Staff::ACCOUNT_TYPE_STORE)->whereIn('status', [Staff::STATUS_DELETED])->get());
        $staff['usedStaff'] = count(Staff::where('company_id', $company['id'])->where('account_type', Staff::ACCOUNT_TYPE_STORE)->whereIn('status', [Staff::STATUS_REGISTER, Staff::STATUS_UNREGISTER, Staff::STATUS_LOCKOUT, Staff::STATUS_ACCOUNT_LOCK])->get());
        $staff['freeStaff'] = $staff['basicStaffPerStore'] + $company['staff_add']['number'] - $staff['usedStaff'];
        //Settings
        $settingDefault = [
            'patientReplySettingMediaid' => ['used' => 0, 'billable' => 1],
            'memberForMessageDeliveryMediaid' => ['used' => 0, 'billable' => 1],
            'hotlineServiceMediaid' => ['used' => 0, 'billable' => 1],
            'hotline24ServiceMediaid' => ['used' => 0, 'billable' => 1]
        ];
        $settings = Setting::where('company_id', $company['id'])->where('name', 'MediaidSettingCompany')->whereIn('key', array_keys($settingDefault))->lists('value', 'key')->toArray();
        foreach ($settingDefault as $k => $v) {
            $settings[$k] = !empty($settings[$k]) ? json_decode($settings[$k], true) : $v;
        }

        $settings['numberCertificatePerStore'] = Setting::mediaidRead('MediaidSettingCompany.numberCertificatePerStore', 0, $company['id']);
        $settings['numberDayDeleteImage'] = Setting::mediaidRead('MediaidSettingCompany.numberDayDeleteImage', 0, $company['id']);
        //Cert
        $allCert = count(Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereNull('company_id')->lists('ssl_client_s_dn_cn', 'id'));
        $cert['availableCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->count();
        $cert['allCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->count();
        $cert['usedCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->where('status', '<>', Certificate::STATUS_NOT_DIVIDE)->whereNotNull('status')->count();
        //First Account
        $firstStaff = Staff::where('company_id', $company['id'])->first();
        $firstStaff = empty($firstStaff) ? [] : $firstStaff->toArray();

        $request->session()->put('tag', $request['tag']);
        return view('mediaid.companies.edit')->with('company', $company)->with(['storePublic' => $store, 'staff' => $staff, 'setting' => $settings, 'firstStaff' => $firstStaff, 'cert' => $cert]);

    }

    public function postUpdateInfoCompany(Request $request)
    {
        $tag = $request['tag'];
        DB::beginTransaction();
        $dataInfoCpn = $request['infoCpn'];
        $company = Company::where('alias', $request['alias'])->first();
        $status = $company->status;
        $contractStore = $company->contract_store;
        $staffAdd = json_decode($company->staff_add, true);
        if (empty($company)) {
            return view('errors.404');
        }
        if (isset($request['infoCpn']['bill_to_postal_code'])) {
            $dataInfoCpn['bill_to_postal_code'] = implode('-', $request['infoCpn']['bill_to_postal_code']);
        }
        if (isset($request['infoCpn']['postal_code_headquarters'])) {
            $dataInfoCpn['postal_code_headquarters'] = implode('-', $request['infoCpn']['postal_code_headquarters']);
        }
        if (isset($request['infoCpn']['staff_add'])) {
            $dataInfoCpn['staff_add'] = json_encode($request['infoCpn']['staff_add']);
        }
        if (isset($request['infoCpn']['cert_add'])) {
            $dataInfoCpn['cert_add'] = json_encode($request['infoCpn']['cert_add']);
        }
        if (isset($request['infoCpn']['billable'])) {
            $billable = json_decode($company['billable'], true);
            foreach ($billable as $k => $v) {
                $billableAfter[$k] = !empty($request['infoCpn']['billable'][$k]) ? $request['infoCpn']['billable'][$k] : $v;
            }
            $dataInfoCpn['billable'] = json_encode($billableAfter);
        }

        $company->fill($dataInfoCpn);
        $company->save();
        //Save data MetaCompnay
        if (!empty($request['meta_company'])) {
            $dataMetaCompany = MetaCompany::where('company_id', $company->id)->first();
            $dataMetaCompany->fill($request['meta_company']);
            $dataMetaCompany->save();
        }
        //Save data Setting Company
        if (isset($request['settings'])) {
            $dataSettingDefault = [
                'patientReplySettingMediaid' => ['used' => 0, 'billable' => 1],
                'memberForMessageDeliveryMediaid' => ['used' => 0, 'billable' => 1],
                'hotlineServiceMediaid' => ['used' => 0, 'billable' => 1],
                'hotline24ServiceMediaid' => ['used' => 0, 'billable' => 1],
            ];
            foreach ($dataSettingDefault as $key => $value) {
                if (isset($request['settings'][$key]['used'])) {
                    $dataSettingDefault[$key]['used'] = $request['settings'][$key]['used'];
                }
                if (isset($request['settings'][$key]['billable'])) {
                    $dataSettingDefault[$key]['billable'] = $request['settings'][$key]['billable'];
                }
                Setting::mediaidWrite('MediaidSettingCompany.' . $key, json_encode($dataSettingDefault[$key]), $company->id);
            }
            Setting::mediaidWrite('MediaidSettingCompany.numberDayDeleteImage', $request['settings']['numberDayDeleteImage'], $company->id);


            //Calculate number cert add new
            $contractStore = $company->contract_store;
            $certInCompany = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->count();
            $certAdd = $request['numberCertificatePerStore'] * $contractStore + $request['infoCpn']['cert_add']['number'] - $certInCompany;

            //Calculate number cert not use
            $cert['availableCount'] = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->count();

            if ($status == Company::STATUS_IN_USE || $request['infoCpn']['status'] == Company::STATUS_IN_USE) {
                if ($certAdd > 0) {
                    for ($i = 1; $i <= $certAdd; $i++) {
                        $certAvailable = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereNull('company_id')->first();
                        if (!empty($certAvailable)) {
                            $update = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->where('id', $certAvailable->id)->whereNull('company_id')->update(['company_id' => $company->id]);
                            if ($update < 0) {
                                DB::rollBack();
                                return redirect()->to(action('Mediaid\CompaniesController@getEdit') . '?id=' . $company->alias)
                                    ->withErrors(['certError' => __('Not enough to issue certificates.')])
                                    ->withInput();
                            }
                        } else {
                            DB::rollBack();
                            return redirect()->to(action('Mediaid\CompaniesController@getEdit') . '?id=' . $company->alias . '&tag=' . $tag)
                                ->withErrors(['certError' => __('Not enough to issue certificates.')])
                                ->withInput();
                        }
                    }
                }
                if ($certAdd < 0) {
                    $certAvailableCount = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->count();
                    if ($certAvailableCount >= abs($certAdd)) {
                        for ($j = 1; $j <= abs($certAdd); $j++) {
                            $certChange = Certificate::whereIsMediaid(Certificate::IS_NOT_MEDIAID)->whereCompanyId($company['id'])->whereStatus(Certificate::STATUS_NOT_DIVIDE)->orWhereNull('status')->first();
                            $certChange->company_id = null;
                            $certChange->save();
                        }
                    } else {
                        DB::rollBack();
                        return redirect()->to(action('Mediaid\CompaniesController@getEdit') . '?id=' . $company->alias . '&tag=' . $tag)
                            ->withErrors(['certError' => __('Not enough to issue certificates.')])
                            ->withInput();
                    }
                }
            }
            Setting::mediaidWrite('MediaidSettingCompany.numberCertificatePerStore', $request['numberCertificatePerStore'], $company->id);
        }

        // Calculator Staffs
        if (isset($request['basicStaffPerStore'])) {
            $basicStaffPerStore = Setting::mediaidRead('MediaidSettingCompany.basicStaffPerStore', 0, $company->id);
            $usedStaff = count(Staff::where('company_id', $company->id)->where('account_type', Staff::ACCOUNT_TYPE_STORE)->whereIn('status', [Staff::STATUS_REGISTER, Staff::STATUS_UNREGISTER, Staff::STATUS_LOCKOUT, Staff::STATUS_ACCOUNT_LOCK])->get());
            $freeStaff = $basicStaffPerStore * $company['contract_store'] + $staffAdd['number'] - $usedStaff;
            $allStaff = $basicStaffPerStore * $contractStore + $staffAdd['number'];
            $staffAddNew = $request['basicStaffPerStore'] * $contractStore + $request['infoCpn']['staff_add']['number'];

            if ($allStaff < $staffAddNew) {
                Setting::mediaidWrite('MediaidSettingCompany.basicStaffPerStore', $request['basicStaffPerStore'], $company->id);
            } else {
                if ($freeStaff >= ($allStaff - $staffAddNew)) {
                    Setting::mediaidWrite('MediaidSettingCompany.basicStaffPerStore', $request['basicStaffPerStore'], $company->id);
                } else {
                    DB::rollBack();
                    return redirect()->to(action('Mediaid\CompaniesController@getEdit') . '?id=' . $company->alias . '&tag=' . $tag)
                        ->withErrors(['staffError' => __('Not enough staffs')])
                        ->withInput();
                }
            }
        }

        //Save Data User Info
        $staff = Staff::where('account_type', Staff::ACCOUNT_TYPE_COMPANY)->where('company_id', $company['id'])->first();
        if (isset($request['staff'])) {
            $dataStaff = $request['staff'];

            $dataStaff['account_type'] = Staff::ACCOUNT_TYPE_COMPANY;
            $dataStaff['status'] = Staff::STATUS_UNREGISTER;
            $dataStaff['last_status'] = $dataStaff['status'];
            $dataStaff['number_login_retry'] = 5;
            $dataStaff['company_id'] = $company['id'];
            if (!empty($staff)) {
                $staff->fill($dataStaff);
                $staff->save();
            } else {
                $staff = Staff::create($dataStaff);
            }

        }
        if ($request['infoCpn']['status'] == Company::STATUS_IN_USE && $request['infoCpn']['status'] != $status) {
            $pass = $this->get_random_string(7);
            $staff->password = Hash::make($pass);
            $staff->must_change_password = 1;
            $staff->number_login_retry = 5;
            $staff->save();
            DB::commit();
            return redirect()->action('Mediaid\CompaniesController@getConfirm')->with('company', ['name' => $company['name'], 'id' => $company['id']])->with('staff', ['username' => $staff->username, 'password' => $pass]);
        }
        DB::commit();
        return redirect()->to(action('Mediaid\CompaniesController@getDetail') . '?id=' . $request['alias']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getConfirm(Request $request)
    {

        return view('mediaid.companies.confirm');
    }

    /**
     * @param $length
     * @param string $valid_chars
     * @return string
     */
    private function get_random_string($length, $valid_chars = '234578ABDEFGHJLMNPRTUYadefghprty')
    {
        $random_string = "";
        $num_valid_chars = strlen($valid_chars);
        for ($i = 0; $i < $length; $i++) {
            $random_pick = mt_rand(1, $num_valid_chars);
            $random_char = $valid_chars[$random_pick - 1];
            $random_string .= $random_char;
        }
        return $random_string;
    }

}