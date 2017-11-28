<?php


namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;
use App\Models\Store;

//use Request;
//use App\Http\Requests;
use App\Models\MessageTemplate;
use App\Models\Setting;
use Validator, Session, Redirect, DB, Auth;


class SettingStoresController extends CompanyAppController
{
    /**
     * Stores setting index page
     * @return $this
     */
    public function getIndex()
    {
        $settingActive = !empty(session('settingActive')) ? session('settingActive') : 1;

        $SettingsTab1Clone = MessageTemplate::where('type', MessageTemplate::TYPE_COMPANY)
            ->whereNull('store_id')
            ->where('company_id', $this->getCurrentCompany('id'))
            ->whereNotNull('copy_from')
            ->lists('copy_from');

        $SettingsTab1 = MessageTemplate::leftJoin('staffs', function ($join) {
            $join->on('message_templates.update_staff_id', '=', 'staffs.id');
        })
            ->select('staffs.*', 'message_templates.*')
            ->where('message_templates.type', MessageTemplate::TYPE_COMPANY)
            ->where(function ($query) use ($SettingsTab1Clone) {
                return $query->where(function ($query) use ($SettingsTab1Clone) {
                    return $query->whereNull('message_templates.copy_from')
                        ->whereNull('message_templates.company_id')
                        ->whereNull('message_templates.store_id')
                        ->where('message_templates.status', MessageTemplate::STATUS_APPLIED)
                        ->whereNotIn('message_templates.id', $SettingsTab1Clone);
                })->Orwhere(function ($query) {
                    return $query->whereNotNull('message_templates.copy_from')
                        ->whereNull('message_templates.store_id')
                        ->where('message_templates.company_id', $this->getCurrentCompany('id'));
                });
            })->get()->toArray();

        $messageCountEdit = MessageTemplate::select('type','status')->where('status','=',MessageTemplate::STATUS_APPLIED)->where('type','=',MessageTemplate::TYPE_GROUP)                                                                    
                                                                    ->where('message_templates.company_id','=',$this->getCurrentCompany('id'))
                                                                    ->count();

        $SettingsTab2 = MessageTemplate::leftJoin('staffs', function ($join) {
            $join->on('message_templates.update_staff_id', '=', 'staffs.id');
        })
	        ->select('staffs.*','message_templates.*')
            ->where('message_templates.type', MessageTemplate::TYPE_GROUP)
            ->whereNull('message_templates.store_id')
            ->where('message_templates.company_id', $this->getCurrentCompany('id'))
            ->get()->toArray();

        // Get setting Store

        $staffLoginSetting        = json_decode(Setting::companyRead('CompanyStoreSetting.setting_staff_login', '{}'));
        $staffLoginSettingCompany = json_decode(Setting::companyRead('CompanySettingChangePass.setting_staff_login', '{}'));

        if (empty ($staffLoginSetting->password_expire) && empty ($staffLoginSetting->multi_account_login)) {
            $staffLoginSetting = (object)['password_expire' => '90days', 'multi_account_login' => true];
        }

        if (empty ($staffLoginSettingCompany->password_expire) && empty ($staffLoginSettingCompany->multi_account_login)) {
            $staffLoginSettingCompany = (object)['password_expire' => '30days', 'multi_account_login' => true];
        }

        $storeInputSetting = Store::getStoreInputSetting();

        return view('company.settingStore.index', compact('settingActive', 'SettingsTab1', 'SettingsTab2', 'staffLoginSetting', 'storeInputSetting','staffLoginSettingCompany'));
    }



    /**
     * Store info settings
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInfoStore()
    {

        $setting = Store::getStoreInputSetting();

        return view('company.settingStore.editInfoStore', compact('setting'));
    }

    /**
     * Saves store info settings
     * @return mixed
     */
    public function postInfoStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accept_credit_card' => 'required',
            'park_info' => 'min:2|max:40',
            'description' => 'min:2|max:40',
            'note_working_time' => 'min:20|max:70',
        ]);

        if ($validator->fails()) {
            return redirect($request->getUri())
                ->withErrors($validator)
                ->withInput();
        }

        $setting = Store::getStoreInputSetting();
        $setting->accept_credit_card['data']['accept'] = $request->input('accept_credit_card') == 0 ? false : true;
        $setting->accept_credit_card['data']['card_type'] = $request->input('credit_card_type');
        $setting->park_info['data'] = $request->input('park_info');
        $setting->description['data'] = $request->input('description');
        $setting->note_working_time['data'] = $request->input('note_working_time');

        Setting::companyWrite('CompanyStoreSetting.store_info_input', $setting);

        return redirect()->to(action('Company\SettingStoresController@getIndex'));
    }

    public function getEdit($id)
    {

        $editMessage = MessageTemplate::findByAlias($id);
        //dd($editMessage);

        $countEditTemp = MessageTemplate::select('type','status')->where('status','=',MessageTemplate::STATUS_APPLIED)
                                                             ->where('type','=',MessageTemplate::TYPE_GROUP)                                                                    
                                                             ->where('message_templates.company_id','=',$this->getCurrentCompany('id'))
                                                             ->whereNull('message_templates.store_id')                                                            
                                                             ->count();

        if (!empty($editMessage->type)) {
            session(['settingActive' => $editMessage->type]);
        }

        if (isset($editMessage) && isset($countEditTemp)) {
            return view('company.settingStore.editMessTemp', compact('editMessage','countEditTemp'));
        } else {
            return redirect()->to(action('Company\SettingStoresController@getIndex'));
        }
    }

    public function postEdit(Request $request)
    {
        $input = $request->all();

        $obj = MessageTemplate::findOrFail($input['id']);

        $validator = Validator::make($request->all(), [
            'txtName' => 'required',
            'txtTitle' => 'required',
            'txtContent' => 'required'
        ],
            [
                'txtName.required' => 'テンプレート名が入力されていません。',
                'txtTitle.required' => 'タイトルが入力されていません。',
                'txtContent.required' => '本文が入力されていません。'
            ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
            // return r_err($validator->errors());
        }

        $type = $obj->type;
        $message_type = $obj->message_type;

        if (empty($obj->copy_from) && $type == MessageTemplate::TYPE_COMPANY) {
            $obj = new MessageTemplate();
            $obj->copy_from = $request['id'];
            $obj->company_id = $this->getCurrentCompany('id');
            $obj->update_staff_id = $this->getCurrentStaff('id');
            $obj->type = $type;
            $obj->message_type = $message_type;
        }

        $obj->status = $request['status'];
        $obj->name = $request['txtName'];
        $obj->title = $request['txtTitle'];
        $obj->content = $request['txtContent'];


        if (isset($request['txtSelect'])) {
            $obj->message_type = $request['txtSelect'];
        }

        $obj->save();

        $obj->name = $input['txtName'];

        return redirect()->to(action('Company\SettingStoresController@getIndex'));
    }

    public function getDestroy($id)
    {
        MessageTemplate::findByAlias($id)->delete();

        return redirect()->to(action('Company\SettingStoresController@getIndex'));
    }

    public function getAdd()
    {
        $countEdit = MessageTemplate::select('type','status')->where('status','=',MessageTemplate::STATUS_APPLIED)
                                                             ->where('type','=',MessageTemplate::TYPE_GROUP)                                                                    
                                                             ->where('message_templates.company_id','=',$this->getCurrentCompany('id'))
                                                             ->whereNull('message_templates.store_id')                                                            
                                                             ->count();
        //dd($countEdit);                                                            
        return view('company.settingStore.add',compact('countEdit'));
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txtName' => 'required',
            'txtTitle' => 'required',
            'txtContent' => 'required'
        ],
            [
                'txtName.required' => 'テンプレート名が入力されていません。',
                'txtTitle.required' => 'タイトルが入力されていません。',
                'txtContent.required' => '本文が入力されていません。'
            ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $insert = new MessageTemplate;
        $insert->message_type = $request->txtSelect;
        $insert->name = $request->txtName;
        $insert->title = $request->txtTitle;
        $insert->content = $request->txtContent;
        $insert->company_id = $this->getCurrentCompany('id');
        $insert->update_staff_id = $this->getCurrentStaff('id');
        $insert->type = MessageTemplate::TYPE_GROUP;
        $insert->status = $request->input('status') == null ? 0 : $request->input('status');
        $insert->save();

		session(['settingActive' => 1]);
    	return redirect()->to(action('Company\SettingStoresController@getIndex'));

    }

    /**
     * Saves login settings
     * @param Request $request
     * @return mixed
     */
    public function postSettingStaff(Request $request)
    {
        $data = $request->all();
        $val = json_encode(['password_expire' => $data['password_expire'], 'multi_account_login' => $data['multi_account_login'] == 1 ? true : false]);

        Setting::companyWrite('CompanyStoreSetting.setting_staff_login', $val);

        Setting::companyWrite('CompanySettingChangePass.setting_staff_login', $val);
        

        return redirect()->to(action('Company\SettingStoresController@getIndex'));
    }

    /**
     * Saves store input settings
     * @param Request $request
     * @return mixed
     */
    public function postSettingStore(Request $request)
    {

        $data = $request->all();

        $settings = Store::getStoreInputSetting(true);
        
        foreach ($settings as $k => $v) {
            $settings[$k]['display'] = !isset($data['display'][$k]) ? $settings[$k]['display'] : !!$data['display'][$k];

            if ($settings[$k]['display']) {
                $settings[$k]['edit'] = !isset($data['edit'][$k]) ? $settings[$k]['edit'] : !!$data['edit'][$k];
            } else {
                $settings[$k]['edit'] = false;
            }
        }

        Setting::companyWrite('CompanyStoreSetting.store_info_input', $settings);

        return redirect()->to(action('Company\SettingStoresController@getIndex'));
    }
}