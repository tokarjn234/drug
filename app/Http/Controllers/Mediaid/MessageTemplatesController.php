<?php

namespace App\Http\Controllers\Mediaid;
use Illuminate\Http\Request;
use App\Models\MessageTemplate;
use App\Models\Store;
use Validator;

class MessageTemplatesController extends MediaidAppController
{
	public function getIndex()
	{
		$settingActive = !empty(session('settingActive')) ? session('settingActive') : 1;

		$SettingsTab1 = MessageTemplate::leftJoin('staffs', function ($join) {
            $join->on('message_templates.update_staff_id', '=', 'staffs.id');
        })
	        ->select('staffs.*','message_templates.*')
	        ->where('message_templates.type','=',MessageTemplate::TYPE_COMPANY)
	        ->whereNull('message_templates.store_id')
            ->whereNull('message_templates.company_id')
	        ->get()->toArray();

		return view('mediaid.messageTemplates.index',compact('SettingsTab1','settingActive'));
	}

	public function getEdit($id)
    {

        $editMessage = MessageTemplate::findByAlias($id);
        //dd($editMessage);

        if (!empty($editMessage->type)) {
            session(['settingActive' => $editMessage->type]);
        }

        if (isset($editMessage)) {
            return view('mediaid.messageTemplates.edit', compact('editMessage'));
        } else {
            return redirect()->to(action('Mediaid\MessageTemplatesController@getIndex'));
        }
    }

    public function postEdit(Request $request)
    {
        $input = $request->all();
        //dd($input);

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


        $obj->name = $input['txtName'];

        $obj->type = MessageTemplate::TYPE_COMPANY;
        $obj->title = $input['txtTitle'];
        $obj->content = $input['txtContent'];
        if (isset($input['txtSelect'])) {
            $obj->message_type = $input['txtSelect'];
        }
        $obj->status = $input['status'];
        $obj->update_staff_id = $this->getCurrentStaff('id');
        
        $obj->save();
        session(['settingActive' => 1]);
        return redirect()->to(action('Mediaid\MessageTemplatesController@getIndex'));
    }

    public function getDestroy($id)
    {
        MessageTemplate::findByAlias($id)->delete();

        return redirect()->to(action('Mediaid\MessageTemplatesController@getIndex'));
    }

    public function getAdd()
    {
        return view('mediaid.messageTemplates.add');
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
        $insert->update_staff_id = $this->getCurrentStaff('id');
        $insert->type = MessageTemplate::TYPE_COMPANY;
        $insert->status = $request->input('status') == null ? 0 : $request->input('status');
        $insert->save();

		session(['settingActive' => 1]);
    	return redirect()->to(action('Mediaid\MessageTemplatesController@getIndex'));
    }
}