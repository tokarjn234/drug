<?php


namespace App\Http\Controllers\Company;


use App\Models\Company;
use App\Models\MetaCompany;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffsController extends CompanyAppController
{

    /**
     * Staff account list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getIndex(Request $request)
    {
        $action = $request['act'];
        if ($action != 'search') {
            $request->session()->forget('valueSearch');
        }
        $companyId = $this->getCurrentCompany('id');
        $limit = 10;
        $search = session('valueSearch');
        if (empty($search)) {
            $search = [
                'store_name' => '',
                'staff_name' => '',
                'deleted_staff' => ''
            ];
        }
        $storeName = $search['store_name'];
        $staffName = $search['staff_name'];
        $storeName = trim($storeName);
        $staffName = trim($staffName);
//            $staffName = str_replace(' ', '%', $staffName);
        $status = array(
            'STATUS_LOCKOUT' => Staff::STATUS_LOCKOUT,
            'STATUS_ACCOUNT_LOCK' => Staff::STATUS_ACCOUNT_LOCK,
            'STATUS_UNREGISTER' => Staff::STATUS_UNREGISTER,
            'STATUS_DELETED' => Staff::STATUS_DELETED,
            'STATUS_REGISTER' => Staff::STATUS_REGISTER
        );
        if ($search['deleted_staff'] == 'on') {
            unset($status['STATUS_DELETED']);
        } else {
            $status['STATUS_DELETED'] = Staff::STATUS_DELETED;
        }

        if ($storeName != '') {

            $paginate = Staff::select('staffs.id', 'staffs.created_at',
                'staffs.first_name', 'staffs.last_name', 'staffs.first_name_kana', 'staffs.last_name_kana', 'staffs.job_category', 'staffs.position',
                'staffs.gender', 'staffs.birthday', 'staffs.last_store_access_id', 'staffs.alias', 'staffs.status', 'staffs.last_access_at', 'staffs.username'
            )
                ->orSearch(DB::raw('CONCAT(`first_name_kana`,`last_name_kana`)'), DB::raw('CONCAT(`first_name`,`last_name`)'), $staffName, ' ', $staffName, ' ')
                ->search('stores.name', $storeName, ' ')
                ->where('staffs.company_id', $companyId)
                ->where('staffs.account_type', Staff::ACCOUNT_TYPE_STORE)
                ->whereIn('staffs.status', $status)
                ->orderBy('staffs.created_at', 'DESC')
                ->join('stores', 'stores.id', '=', 'staffs.last_store_access_id')
                ->paginate($limit);

        } else {
            $paginate = Staff::select('id', 'created_at',
                'first_name', 'last_name', 'first_name_kana', 'last_name_kana', 'job_category', 'position',
                'gender', 'birthday', 'last_store_access_id', 'alias', 'status', 'last_access_at', 'username'
            )
                ->search(DB::raw('CONCAT(`first_name`,`last_name`)'), $staffName, ' ')
                ->orSearch(DB::raw('CONCAT(`first_name_kana`,`last_name_kana`)'), DB::raw('CONCAT(`first_name`,`last_name`)'), $staffName, ' ', $staffName, ' ')
                ->whereAccountType(Staff::ACCOUNT_TYPE_STORE)
                ->where('company_id', $companyId)
                ->whereIn('staffs.status', $status)
                ->orderBy('created_at', 'DESC')
                ->paginate($limit);
        }
        if ($action == 'search') {
            $paginate->appends(['act' => 'search']);
        }

        $staff = Staff::render($paginate);
        if (!empty($staff)) {
            foreach ($staff as $key => $value) {
                $store = Store::select('stores.name')->where('id', $value['last_store_access_id'])->first();
                $staff[$key]['last_view_store'] = empty($store) ? '-' : $store->name;
            }
        }
        $numberStaff['staff_deleted'] = count(Staff::where('account_type', Staff::ACCOUNT_TYPE_STORE)->where('company_id', $companyId)->where('status', Staff::STATUS_DELETED)->get());
        // Default

        $basicStaffPerStore = Setting::mediaidRead('MediaidSettingCompany.basicStaffPerStore', 0, $this->getCurrentCompany('id'));
        $accountStaff = Company::where('id', $this->getCurrentCompany('id'))->first();
        $staffAdd = json_decode($accountStaff->staff_add, true);

        $stNumberStaff = $basicStaffPerStore * $accountStaff->contract_store + $staffAdd['number'];
        Setting::companyWrite('CompanyStaffSetting.all_staff', $stNumberStaff);

        $numberStaff['all_staff'] = $stNumberStaff;

        $numberStaff['staff_used'] = count(Staff::where('account_type', Staff::ACCOUNT_TYPE_STORE)->where('company_id', $companyId)->where('status', '<>', Staff::STATUS_DELETED)->lists('id'));
        $numberStaff['staff_available'] = ($numberStaff['all_staff'] - $numberStaff['staff_used'] >= 0) ? $numberStaff['all_staff'] - $numberStaff['staff_used'] : 0;
        $numberStaff['staff_outstanding'] = count(Staff::where('account_type', Staff::ACCOUNT_TYPE_STORE)->where('company_id', $companyId)->lists('id'));
        $numberStaff['store_introduction'] = count(Store::where('company_id', $this->getCurrentCompany('id'))->whereNull('is_deleted')->get());

        // -----------------------

        $valueSearch = array(
            'store_name' => $search['store_name'],
            'staff_name' => $search['staff_name'],
            'deleted_staff' => $search['deleted_staff'],
        );

        $request->session()->put(['valueSearch' => $valueSearch]);

        return view('company.staff.index')->with(['staff' => $staff, 'paginate' => $paginate, 'number' => $numberStaff, 'valueSearch' => $valueSearch]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postIndex(Request $request)
    {
        $valueSearch = array(
            'store_name' => $request['store_name'],
            'staff_name' => $request['staff_name'],
            'deleted_staff' => $request['deleted_staff'],
        );
        $request->session()->put(['valueSearch' => $valueSearch]);
        return redirect()->to(action('Company\StaffsController@getIndex') . '?act=search');
    }

    /**
     * @param
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreate()
    {
        $rand = DB::select('SELECT FLOOR(RAND() * 9999999) AS random_num
                            FROM staffs
                            WHERE "random_num" NOT IN (SELECT username FROM staffs WHERE status <> ? AND company_id=?)
                            LIMIT 1', [Staff::STATUS_DELETED, $this->getCurrentCompany('id')]
        );

        return !empty($rand) ? view('company.staff.create')->with(['new_id' => $rand[0]->random_num]) : view('errors.404');
    }

    public function postCreateIdStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->action('Company\StaffsController@getCreate')->with('errors', $validator->errors()->all())->with('new_id', $request['member_id'])->withInput();
        }

        $pass = $this->get_random_string(7);
        $data = [
            'company_id' => $this->getCurrentCompany('id'),
            'password' => Hash::make($pass),
            'status' => Staff::STATUS_UNREGISTER,
            'account_type' => Staff::ACCOUNT_TYPE_STORE,
            'last_status' => Staff::STATUS_UNREGISTER
        ];
        if (!empty($request['member_id'])) {
            $data['username'] = $request['member_id'];
        }

        $staffId = Staff::where('username', $request['member_id'])->where('company_id', $this->getCurrentCompany('id'))->first();
        if (!empty($staffId)) {
            if ($staffId->status != Staff::STATUS_DELETED) {
                return redirect()->action('Company\StaffsController@getCreate')->with('errors', ['ID Staff already exist'])->with('new_id', $request['member_id'])->withInput();
            } else {
                Staff::where('username', $request['member_id'])->delete();
            }

        }
        $staff = Staff::create($data);
        $staff->save();


        return redirect()->action('Company\StaffsController@getConfirm')
            ->with(['idStaff' => $request['member_id'], 'password' => $pass, 'company_id' => $this->getCurrentCompany('id'), 'mess' => 'アカウントを発行しました。']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getConfirm()
    {
        return view('company.staff.confirm');
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

        Staff::where('alias', $request['id'])->update(['password' => Hash::make($newPass), 'must_change_password' => 1, 'number_login_retry' => env('NUMBER_LOGIN_RETRY', 5), 'status' => $staffLastStatus]);
        return view('company.staff.confirm')->with(['idStaff' => $staffId, 'pass' => $newPass, 'company_id' => $this->getCurrentCompany('id'), 'mess' => 'パスワードをリセットしました。']);
    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getDetail(Request $request)
    {
        $staff = Staff::where('alias', $request['id'])->first();
        if (empty($staff)) {
            return view('errors.404');
        } else {
            $staffRender['data'] = $staff->toArray();
        }
        $staffRender = Staff::render($staffRender);
        $request->session()->put('curent_staff', $staffRender['data']['alias']);

        return view('company.staff.detail')->with(['staff' => $staffRender['data']]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getDelete(Request $request)
    {
        $alias = $request->session()->pull('curent_staff');

        $staff = Staff::where('alias', $alias)->first();
//        $data['last_status'] = $staff->status;
        if (isset($request['stt'])) {
            if ($request['stt'] == 'del') {
                $data['status'] = Staff::STATUS_DELETED;
                $data['deleted_at'] = date('Y-m-d H:i:s', time());
            }
            if ($request['stt'] == 'lock') {
                $data['status'] = Staff::STATUS_ACCOUNT_LOCK;
            }
            if ($request['stt'] == 'unlock') {
                $data['status'] = $staff->last_status;
            }
            if (isset($data['status'])) {
                Staff::where('alias', $alias)->update($data);
            }
        }
        $request->session()->forget('curent_staff');
        return redirect()->action('Company\StaffsController@getIndex');
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
}