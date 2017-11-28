<?php

namespace App\Http\Controllers\Mediaid;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MediaidAccountsController extends MediaidAppController
{
	public function getIndex()
	{	
        $staffs = Staff::whereAccountType(Staff::ACCOUNT_TYPE_MEDIAID)
                       ->orderBy('staffs.id','DESC')
                       ->paginate(10);

		return view('mediaid.mediaidAccount.index',compact('staffs'));
	}

	public function getCreate() {

        return view('mediaid.mediaidAccount.create');
    }

    public function postUpdate(Request $request){
        $input = $request->all();
        //dd($input);
        //$companyId = $this->getCurrentCompany('id');

        if (Staff::whereUsername($request->input('username'))->count() > 0) {
            return redirect()->action('Mediaid\MediaidAccountsController@getCreate')
                ->withErrors([__('AccountIdExisted')])
                ->withInput();
        }

        return view('mediaid.mediaidAccount.update',compact('input'));
    }

	public function getConfirm()
    {
        return view('mediaid.mediaidAccount.confirm');
    } 

    public function postCreateIdStaff(Request $request)
    {
       	$input = $request->all();
       	//dd($input);

        $pass = $this->get_random_string(7);
        $data = [
        	'first_name' => $request->input('firstName'),
        	'username' => $request->input('name'),
        	'last_name' => $request->input('lastName'),
        	'first_name_kana' => $request->input('first_name_kana'),
        	'last_name_kana' => $request->input('last_name_kana'),
        	'authority' => $request->input('Authority'),
            'password' => Hash::make($pass),
            'status' => Staff::STATUS_UNREGISTER,
            'account_type' => Staff::ACCOUNT_TYPE_MEDIAID
        ];
        //dd($data);
        $staff = Staff::create($data);
        
        $staff->save();


        return redirect()->action('Mediaid\MediaidAccountsController@getConfirm')
        				 ->with(['idStaff' => $request['name'], 'password' => $pass, 'mess' => 'アカウントを発行しました。']);
    }

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

    public function postRemoveStaff(Request $request) {
        $staff = Staff::where('alias', '!=', $this->getCurrentStaff('alias'))           
                      ->whereAlias($request->input('alias'))->first();

        if (empty ($staff)) {
            return redirect()->action('Mediaid\MediaidAccountsController@getIndex');
        }

        $staff->update(['status' => Staff::STATUS_DELETED]);

        return redirect()->action('Mediaid\MediaidAccountsController@getIndex');

    }

    public function postDelete(Request $request)
    {
        $data = $request->all();

        $staff = Staff::where('alias', $data['id'])->first();
        if(empty($staff)){
            return new Exception("Error Processing Request", 1);
        }
        // pr($data);die;
        if($data['stt'] == 'changePass'){
            //Change pass
            $newPass = $this->get_random_string(7);
            $staffLastStatus = $staff->last_status;
            $staffId = $staff->username;

            Staff::where('alias', $request['id'])->update(['password' => Hash::make($newPass), 'must_change_password' => 1, 'number_login_retry' => env('NUMBER_LOGIN_RETRY', 5), 'status' => $staffLastStatus]);
            return redirect()->action('Mediaid\MediaidAccountsController@getConfirm')->with(['idStaff' => $staffId, 'password' => $newPass, 'mess' => 'パスワードをリセットしました。']);
        }
        if($data['stt'] == 'delete'){
            //Delete
            $staff->status = Staff::STATUS_DELETED;
            $staff->save();
        }
        if($data['stt'] == 'lockAccount'){
            //Lock
            $staff->status = Staff::STATUS_ACCOUNT_LOCK;
            $staff->save();
        }
        if($data['stt'] == 'unLockAccount'){
            //Lock
            $staff->status = empty($staff->last_status)?Staff::STATUS_UNREGISTER:$staff->last_status;
            $staff->last_status = $staff->status;
            $staff->save();
        }

        return redirect()->action('Mediaid\MediaidAccountsController@getIndex');
    }

}