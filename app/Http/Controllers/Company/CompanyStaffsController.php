<?php


namespace App\Http\Controllers\Company;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyStaffsController extends CompanyAppController
{
    /**
     * Company staffs list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $staffs = Staff::whereAccountType(Staff::ACCOUNT_TYPE_COMPANY)
            ->where('company_id', '=', $this::getCurrentCompany('id'))
            ->orderBy('staffs.id', 'DESC')
            ->paginate(10);
//        pr($staffs->toArray());die;

        return view('company.company_staffs.index', compact('staffs'));
    }

    /**
     * Creates new account
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreate()
    {

        $rand = DB::select('SELECT FLOOR(RAND() * 9999999) AS random_num
                            FROM staffs
                            WHERE "random_num" NOT IN (SELECT username FROM staffs WHERE status <> ? AND company_id=?)
                            LIMIT 1', [Staff::STATUS_DELETED, $this->getCurrentCompany('id')]
        );

        return !empty($rand) ? view('company.company_staffs.create')->with(['new_id' => $rand[0]->random_num]) : view('errors.404');

    }


    /**
     * Company\CompanyStaffsController@postRemoveStaff
     * Removes staff
     * @param Request $request
     */
    public function postRemoveStaff(Request $request)
    {
        $staff = Staff::whereCompanyId($this->getCurrentCompany('id'))
            ->where('alias', '!=', $this->getCurrentStaff('alias'))
            ->whereAlias($request->input('alias'))->first();

        if (empty ($staff)) {
            return redirect()->action('Company\CompanyStaffsController@getIndex');
        }

        $staff->update(['status' => Staff::STATUS_DELETED]);

        return redirect()->action('Company\CompanyStaffsController@getIndex');

    }


    /**
     *  Company\CompanyStaffsController@getChangePassword
     */
    public function getChangePassword($alias = '')
    {
        $staff = Staff::whereCompanyId($this->getCurrentCompany('id'))
            ->where('alias', '!=', $this->getCurrentStaff('alias'))
            ->whereAlias($alias)->first();
        //dd($staff);
        if (empty ($staff)) {
            throw new \Exception('Staff not found');
        }

        return view('company.company_staffs.change_password', compact('staff'));
    }

    /**
     *  Company\CompanyStaffsController@postChangePassword
     */
    public function postChangePassword(Request $request)
    {
        $staff = Staff::whereCompanyId($this->getCurrentCompany('id'))
            ->where('alias', '!=', $this->getCurrentStaff('alias'))
            ->whereAlias($request->input('alias'))->first();

        if (empty ($staff)) {
            throw new \Exception('Staff not found');
        }

        $staff->password = \Hash::make($request->input('password'));
        $staff->save();

        return redirect()->action('Company\CompanyStaffsController@getIndex');
    }

    public function postUpdate(Request $request)
    {
        $input = $request->all();
        //dd($input);
        $companyId = $this->getCurrentCompany('id');

        if (Staff::whereCompanyId($companyId)->whereUsername($request->input('username'))->count() > 0) {
            return redirect()->action('Company\CompanyStaffsController@getCreate')
                ->withErrors([__('AccountIdExisted')])
                ->withInput();
        }

        return view('company.company_staffs.update', compact('input'));
    }

    public function postUpdateData(Request $request)
    {
        $input = $request->all();
        //dd($input);
        $companyId = $this->getCurrentCompany('id');
        $addStaff = new staff();
        $addStaff->account_type = Staff::ACCOUNT_TYPE_COMPANY;
        $addStaff->first_name = $request->input('firstName');
        $addStaff->username = $request->input('name');
        $addStaff->company_id = $this->getCurrentCompany('id');
        $addStaff->last_name = $request->input('lastName');
        $addStaff->first_name_kana = $request->input('first_name_kana');
        $addStaff->last_name_kana = $request->input('last_name_kana');
        $addStaff->job_category = $request->input('job_category');
        $addStaff->status = Staff::STATUS_UNREGISTER;
        $addStaff->last_status = Staff::STATUS_UNREGISTER;
        $pass = $this->get_random_string(7);
        $addStaff->password = Hash::make($pass);

        if ($addStaff->save()) {
            $newStaff = Staff::whereCompanyId($this->getCurrentCompany('id'))->whereId($addStaff['id'])->first();
            return view('company.company_staffs.confirm')->with(['idStaff' => $addStaff['username'], 'pass' => $pass, 'mess' => 'アカウントを発行しました。', 'company_id' => $companyId]);
        } else {
            throw new \Exception('Could not update profile');
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getNewPassword(Request $request)
    {
        if (!isset($request['id'])) {
            return view('errors.404');
        }

        $staffId = Staff::findByAlias($request['id'], 'username');
        $staffLastStatus = Staff::findByAlias($request['id'], 'last_status');
        if (empty($staffId)) {
            return view('errors.404');
        }
        $newPass = $this->get_random_string(7);
        $mess = 'パスワードをリセットしました。';
        $companyId = $this->getCurrentCompany('id');
        Staff::where('alias', $request['id'])->update(['password' => Hash::make($newPass), 'must_change_password' => 1, 'number_login_retry' => env('NUMBER_LOGIN_RETRY', 5), 'status' => $staffLastStatus]);
        return view('company.company_staffs.confirm')->with(['idStaff' => $staffId, 'pass' => $newPass, 'mess' => $mess, 'company_id' => $companyId]);

    }

    public function getConfirm()
    {
        $companyId = $this->getCurrentCompany('id');
        return view('company.company_staffs.confirm')->with('company_id', $companyId);
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

    public function postDelete(Request $request)
    {
        $data = $request->all();

        $staff = Staff::where('alias', $data['id'])->first();
        if (empty($staff)) {
            return new Exception("Error Processing Request", 1);
        }
        // pr($data);die;
        if ($data['stt'] == 'changePass') {
            //Change pass
            $newPass = $this->get_random_string(7);
            $staffLastStatus = $staff->last_status;
            $staffId = $staff->username;

            Staff::where('alias', $request['id'])->update(['password' => Hash::make($newPass), 'must_change_password' => 1, 'number_login_retry' => env('NUMBER_LOGIN_RETRY', 5), 'status' => $staffLastStatus]);
            return redirect()->action('Company\CompanyStaffsController@getConfirm')->with(['idStaff' => $staffId, 'password' => $newPass, 'mess' => 'パスワードをリセットしました。']);
        }
        if ($data['stt'] == 'delete') {
            //Delete
            $staff->status = Staff::STATUS_DELETED;
            $staff->save();
        }
        if ($data['stt'] == 'lockAccount') {
            //Lock
            $staff->status = Staff::STATUS_ACCOUNT_LOCK;
            $staff->save();
        }
        if ($data['stt'] == 'unLockAccount') {
            //Lock
            $staff->status = empty($staff->last_status) ? Staff::STATUS_UNREGISTER : $staff->last_status;
            $staff->last_status = $staff->status;
            $staff->save();
        }

        return redirect()->action('Company\CompanyStaffsController@getIndex');
    }
}