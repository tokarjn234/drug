<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
//use Request;
use App\Http\Requests;
use App\Models\MessageTemplate;
use App\Models\Setting;
use Validator, Session, Redirect, DB, Auth;

class SettingsController extends HomeAppController
{
    /**
     * Message list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getIndex()
    {
        $settingActive = !empty(session('settingActive')) ? session('settingActive') : 2;

        $SettingsTab1StoreClone = MessageTemplate::where('type', MessageTemplate::TYPE_COMPANY)
            ->where('store_id', $this->getCurrentStore('id'))
            ->where('company_id', $this->getCurrentCompany('id'))
            ->whereNotNull('copy_from')
            ->lists('copy_from');

        $SettingsTab1CompanyClone = MessageTemplate::where('type', MessageTemplate::TYPE_COMPANY)
            ->whereNull('store_id')
            ->where('company_id', $this->getCurrentCompany('id'))
            ->whereNotNull('copy_from')
            ->lists('copy_from');

        $SettingsTab1 = MessageTemplate::leftJoin('staffs', function ($join) {
            $join->on('message_templates.update_staff_id', '=', 'staffs.id');
        })
            ->select('staffs.*', 'message_templates.*')
            ->where('message_templates.type', MessageTemplate::TYPE_COMPANY)
            ->where(function ($query) use ($SettingsTab1StoreClone, $SettingsTab1CompanyClone) {
                return $query->where(function ($query) use ($SettingsTab1StoreClone, $SettingsTab1CompanyClone) {
                    return $query->whereNull('message_templates.copy_from')
                        ->whereNull('message_templates.company_id')
                        ->where('message_templates.status', MessageTemplate::STATUS_APPLIED)
                        ->whereNotIn('message_templates.id', $SettingsTab1StoreClone)
                        ->whereNotIn('message_templates.id', $SettingsTab1CompanyClone);
                })->Orwhere(function ($query) use ($SettingsTab1StoreClone) {
                    return $query->whereNotNull('message_templates.copy_from')
                                ->where('message_templates.company_id', $this->getCurrentCompany('id'))
                                ->where(function ($query) {
                                    return $query->where('message_templates.store_id', $this->getCurrentStore('id'));
                                })->orWhere(function ($query) use ($SettingsTab1StoreClone) {
                                   return $query->whereNull('message_templates.store_id')
                                       ->whereNotIn('message_templates.copy_from', $SettingsTab1StoreClone)
                                       ->whereNotIn('message_templates.id', $SettingsTab1StoreClone)
                                       ->where('message_templates.status', MessageTemplate::STATUS_APPLIED);
                                });
                });
            })->get()->toArray();

        $SettingsTab2Clone = MessageTemplate::where('type', MessageTemplate::TYPE_GROUP)
            ->where('store_id', $this->getCurrentStore('id'))
            ->where('company_id', $this->getCurrentCompany('id'))
            ->whereNotNull('copy_from')
            ->lists('copy_from');

        $SettingsTab2NotCurrentStore = MessageTemplate::where('type', MessageTemplate::TYPE_GROUP)
            ->where('store_id', '!=', $this->getCurrentStore('id'))
            ->where('company_id', $this->getCurrentCompany('id'))
            ->lists('id');

        $SettingsTab2 = MessageTemplate::leftJoin('staffs', function ($join) {
            $join->on('message_templates.update_staff_id', '=', 'staffs.id');
        })
            ->select('staffs.*', 'message_templates.*')
            ->where('message_templates.type', MessageTemplate::TYPE_GROUP)
            ->where('message_templates.company_id', $this->getCurrentCompany('id'))
            ->whereNotIn('message_templates.id', $SettingsTab2NotCurrentStore)
            ->where(function ($query) use ($SettingsTab2Clone, $SettingsTab2NotCurrentStore) {
                return $query->where(function ($query) use ($SettingsTab2Clone, $SettingsTab2NotCurrentStore) {
                    return $query->whereNull('message_templates.copy_from')
                        ->where('message_templates.status', MessageTemplate::STATUS_APPLIED)
                        ->whereNotIn('message_templates.id', $SettingsTab2Clone);
                })
                    ->Orwhere(function ($query) {
                        return $query->whereNotNull('message_templates.copy_from')
                            ->where('message_templates.store_id', $this->getCurrentStore('id'));
                    });
            })->get()->toArray();

        $SettingsTab3 = MessageTemplate::leftJoin('staffs', function ($join) {
            $join->on('message_templates.update_staff_id', '=', 'staffs.id');
        })
            ->select('staffs.*', 'message_templates.*')
            ->where('message_templates.type', MessageTemplate::TYPE_STORE)
            ->where('message_templates.store_id', $this->getCurrentStore('id'))
            ->where('message_templates.company_id', $this->getCurrentCompany('id'))
            ->get()->toArray();

        $acceptOrderOnNonBusinessHour = Setting::read('StoreTimeSetting.acceptOrderOnNonBusinessHour', false) ? Setting::read('StoreTimeSetting.acceptOrderOnNonBusinessHour', false) : Setting::companyReadOnly('StoreTimeSetting.acceptOrderOnNonBusinessHour', false);
        $showAlertAtNight = Setting::read('StoreTimeSetting.showAlertAtNight', false) ? Setting::read('StoreTimeSetting.showAlertAtNight', false) : Setting::companyReadOnly('StoreTimeSetting.showAlertAtNight', false);
        $patientReplySetting = Setting::read('StoreTimeSetting.patientReplySetting', false) ? Setting::read('StoreTimeSetting.patientReplySetting', false) : Setting::companyReadOnly('StoreTimeSetting.patientReplySetting', false);
        $settingChangeOnStoreHour = Setting::companyReadOnly('StoreTimeSetting.settingChangeOnStoreHour', false);
        $settingChangeOnStoreAtNight = Setting::companyReadOnly('StoreTimeSetting.settingChangeOnStoreAtNight', false);
        $settingChangeOnStorePatientReply = Setting::companyReadOnly('StoreTimeSetting.settingChangeOnStorePatientReply', false);
        $patientReplySettingMediaid = Setting::mediaidRead('MediaidSettingCompany.patientReplySettingMediaid', '{"used":0,"billable":"1"}', $this->getCurrentCompany('id'));
        $patientReplySettingMediaid = json_decode($patientReplySettingMediaid, true);
        $patientReplySettingMediaid = $patientReplySettingMediaid['used'];


        return view('home.settings.index', compact('SettingsTab1', 'SettingsTab2', 'SettingsTab3',
            'acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting', 'settingActive',
            'settingChangeOnStoreHour', 'settingChangeOnStorePatientReply', 'settingChangeOnStoreAtNight', 'patientReplySettingMediaid'
        ));
    }

    /**
     * Get edit message
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getEdit($id)
    {

        $editMessage = MessageTemplate::findByAlias($id);
        $messageCountEdit = MessageTemplate::select('type', 'status')->where('status', '=', MessageTemplate::STATUS_APPLIED)->where('type', '=', MessageTemplate::TYPE_STORE)
            ->where('message_templates.store_id', '=', $this->getCurrentStore('id'))
            ->where('message_templates.company_id', '=', $this->getCurrentCompany('id'))
            ->count();

        if (isset($editMessage) && isset($messageCountEdit)) {
            return view('home.settings.edit', [
                'editMessage' => $editMessage,
                'messageCountEdit' => $messageCountEdit,

                'jsonData' => [
                    'message' => $editMessage,
                    'messageEdit' => $messageCountEdit
                ]
            ]);
        } else {
            return redirect()->to(action('Home\SettingsController@getIndex'));
        }

    }

    /**
     * Post edit message
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function postEdit(Request $request)
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
            return redirect()->back()->withErrors($validator->errors());
            // return r_err($validator->errors());
        }

        $obj = MessageTemplate::findOrFail($request['id']);

        if (!empty($obj->type)) {
            session(['settingActive' => $obj->type]);
        }

        $type = $obj->type;
        $message_type = $obj->message_type;

        if ((empty($obj->copy_from) && ($type == MessageTemplate::TYPE_COMPANY || $type == MessageTemplate::TYPE_GROUP))
        || (!empty($obj->copy_from) && $type == MessageTemplate::TYPE_COMPANY && empty($obj->store_id)) ) {
            $obj = new MessageTemplate();
            $obj->copy_from = $request['id'];
            $obj->store_id = $this->getCurrentStore('id');
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

        return redirect()->to(action('Home\SettingsController@getIndex'));
    }

    /**
     * Get delete message
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getDestroy($id)
    {
        MessageTemplate::findByAlias($id)->delete();

        return redirect()->to(action('Home\SettingsController@getIndex'));
    }

    /**
     * Get add message
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getAdd()
    {
        $messageCount = MessageTemplate::select('type', 'status')->where('status', '=', MessageTemplate::STATUS_APPLIED)->where('type', '=', MessageTemplate::TYPE_STORE)
            ->where('message_templates.store_id', '=', $this->getCurrentStore('id'))
            ->where('message_templates.company_id', '=', $this->getCurrentCompany('id'))
            ->count();
        //dd($messageCount);
        return view('home.settings.add', [
            'jsonData' => [
                'messageCount' => $messageCount
            ]
        ]);
    }

    /**
     * Post add message
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

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


        $valueInput = $request->all();
        //dd($valueInput);

        $insert = new MessageTemplate;

        $insert->message_type = $request->txtSelect;
        $insert->name = $request->txtName;
        $insert->title = $request->txtTitle;
        $insert->content = $request->txtContent;
        $insert->store_id = $this->getCurrentStore('id');
        $insert->company_id = $this->getCurrentCompany('id');
        $insert->update_staff_id = $this->getCurrentStaff('id');
        $insert->status = $request->input('status') == null ? 0 : $request->input('status');
        $insert->type = MessageTemplate::TYPE_STORE;

        $insert->save();
        session(['settingActive' => 3]);
        return redirect()->to(action('Home\SettingsController@getIndex'));
    }

    /**
     * Setting template
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function postSetting(Request $request)
    {

        Setting::write('StoreTimeSetting.acceptOrderOnNonBusinessHour', $request->input('acceptOrderOnNonBusinessHour'));
        Setting::write('StoreTimeSetting.showAlertAtNight', $request->input('showAlertAtNight'));
        Setting::write('StoreTimeSetting.patientReplySetting', $request->input('patientReplySetting'));

        return redirect()->to(action('Home\SettingsController@getIndex'));
    }
}
