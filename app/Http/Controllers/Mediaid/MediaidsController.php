<?php


namespace App\Http\Controllers\Mediaid;

use App\Models\Company;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use DateTime;
use DateInterval;
use DatePeriod;
use Symfony\Component\HttpFoundation\File;
use Validator;
use App\Models\Order;
use App\Models\Province;
use App\Models\Store;
use DB;



class MediaidsController extends MediaidAppController
{
    /**
     * Staffs Statistic list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getIndex()
    {
        $startDate = date("Y-m-01", time());
        $endDate = date('Y-m-d', time());
        $startMonth = date('Y-m-d', strtotime("-11 months"));
        $endMonth = date('Y-m-d', time());

        $dayActive = "";
        $monthActive = "";
        $monthlySummaryActive = "";
        $currentActive = "active in";

        $inputs = session('statisticInputs');

        if (!empty($inputs)) {

            $startDate = !empty(session('statisticStartDate')) ? session('statisticStartDate') : date("Y-m-01", time());
            $endDate = !empty(session('statisticEndDate')) ? session('statisticEndDate') : date('Y-m-d', time());
            $startMonth = !empty(session('statisticStartMonth')) ? session('statisticStartMonth') : date('Y-m-d', strtotime("-11 months"));
            $endMonth = !empty(session('statisticEndMonth')) ? session('statisticEndMonth') : date('Y-m-d', time());

            if (!empty($inputs['startDate'])) {
                $startDate = $this::__parseDate($inputs['startDate']);
            }

            if (!empty($inputs['endDate'])) {
                $endDate = $this::__parseDate($inputs['endDate']);
            }

            if (!empty($inputs['startMonth'])) {
                $startMonth = $this::__parseDate($inputs['startMonth']);
            }

            if (!empty($inputs['endMonth'])) {
                $endMonth = $this::__parseDate($inputs['endMonth']);
            }

            if (isset($inputs['startDate']) && isset($inputs['endDate'])) {
                $dayActive = "active in";
                $monthActive = "";
                $currentActive = "";
                $monthlySummaryActive = "";

            } else if (isset($inputs['startMonth']) && isset($inputs['endMonth'])) {
                $dayActive = "";
                $monthActive = "active in";
                $currentActive = "";
                $monthlySummaryActive = "";
            } else {
                $dayActive = "";
                $monthActive = "";
                $currentActive = "active in";
                $monthlySummaryActive = "";
            }
        }

        session(['statisticStartDate' => $startDate]);
        session(['statisticEndDate' => $endDate]);
        session(['statisticStartMonth' => $startMonth]);
        session(['statisticEndMonth' => $endMonth]);

        $currentDate = date('Y-m-d', time());
        $yesterday = date('Y-m-d', strtotime("-1 days"));

        $viewByDaysCreatedAt = $this::__getDataOrders($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_CREATE_AT);
        $viewByMonthsCreatedAt = $this::__getDataOrders($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_CREATE_AT);

        $viewByDaysPrepared = $this::__getDataOrders($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_PREPARED);
        $viewByMonthsPrepared = $this::__getDataOrders($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_PREPARED);

        $viewByDaysInvalid = $this::__getDataOrders($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_INVALID);
        $viewByMonthsInvalid = $this::__getDataOrders($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_INVALID);

        $viewByCurrent = $this::__getDataOrders($yesterday, $currentDate, Store::VIEW_BY_DAYS, Store::STATUS_CREATE_AT);
        $viewByCurrentPrepared = $this::__getDataOrders($yesterday, $currentDate, Store::VIEW_BY_DAYS, Store::STATUS_PREPARED);
        $viewByCurrentInvalid = $this::__getDataOrders($yesterday, $currentDate, Store::VIEW_BY_DAYS, Store::STATUS_INVALID);

        $viewByDaysUserRegister = $this::__getDataUsers($startDate, $endDate, Store::VIEW_BY_DAYS, 0);
        $viewByDaysUserExited = $this::__getDataUsers($startDate, $endDate, Store::VIEW_BY_DAYS, 1);
        $viewByDaysUserAll = $this::__getDataUsers($startDate, $endDate, Store::VIEW_BY_DAYS, 2);

        $viewByUserRegisterCurrent = $this::__getDataUsers($yesterday, $currentDate, Store::VIEW_BY_DAYS, 0);
        $viewByUserAllCurrent = $this::__getDataUsers($yesterday, $currentDate, Store::VIEW_BY_DAYS, 2);

        $viewByMonthsUserRegister = $this::__getDataUsers($startMonth, $endMonth, Store::VIEW_BY_MONTH, 0);
        $viewByMonthsUserExited = $this::__getDataUsers($startMonth, $endMonth, Store::VIEW_BY_MONTH, 1);
        $viewByMonthsUserAll = $this::__getDataUsers($startMonth, $endMonth, Store::VIEW_BY_MONTH, 2);

        foreach ($viewByCurrent as $key => $current) {
            $viewByCurrent[] = $current;
            unset($viewByCurrent[$key]);
        }

        foreach ($viewByCurrentPrepared as $key => $current) {
            $viewByCurrentPrepared[] = $current;
            unset($viewByCurrentPrepared[$key]);
        }

        foreach ($viewByCurrentInvalid as $key => $current) {
            $viewByCurrentInvalid[] = $current;
            unset($viewByCurrentInvalid[$key]);
        }

        foreach ($viewByUserRegisterCurrent as $key => $current) {
            $viewByUserRegisterCurrent[] = $current;
            unset($viewByUserRegisterCurrent[$key]);
        }

        foreach ($viewByUserAllCurrent as $key => $current) {
            $viewByUserAllCurrent[] = $current;
            unset($viewByUserAllCurrent[$key]);
        }

        $startDate = $this::__convertJapaneseDate($startDate, 1);
        $endDate = $this::__convertJapaneseDate($endDate, 1);
        $startMonth = $this::__convertJapaneseDate($startMonth, 0);
        $endMonth = $this::__convertJapaneseDate($endMonth, 0);

        $companies  = Company::where('status', '!=', Company::STATUS_CANCELLATION_COMPLETED)->select('id','name')->get();

        $search = session('StoresSearchCsvData');
        if ($search && empty($inputs)) {
            $companies = $this->getCompaniesData($search);
        }

        if (session('monthlySummaryActive') && empty($inputs)) {
            $dayActive = "";
            $monthActive = "";
            $currentActive = "";
            $monthlySummaryActive = "active in";
        }

        session(['monthlySummaryActive' => '']);

        session(['statisticInputs' => '']);

        return view("mediaid.mediaids.index", compact(
            'viewByDaysUserRegister', 'viewByDaysUserExited', 'viewByDaysUserAll',
            'viewByMonthsUserRegister', 'viewByMonthsUserExited', 'viewByMonthsUserAll',
            'viewByUserRegisterCurrent', 'viewByUserAllCurrent',
            'viewByDaysCreatedAt', 'viewByMonthsCreatedAt', 'viewByDaysPrepared', 'viewByMonthsPrepared', 'viewByDaysInvalid', 'viewByMonthsInvalid',
            'dayActive', 'currentActive', 'monthActive', 'monthlySummaryActive', 'viewByCurrent', 'viewByCurrentPrepared', 'viewByCurrentInvalid',
            'startDate', 'endDate', 'startMonth', 'endMonth', 'companies', 'search'
        ));
    }

    /**
     * Stores Statistic list
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function postIndex(Request $request)
    {
        session(['statisticInputs' => $request->all()]);
        session(['monthlySummaryActive' => '']);
        return redirect('mediaid/mediaids');

    }

    protected function __groupBy($by, $variable, $x) {
        $date = strtotime($variable[$x]);
        if ($by == 0) {
            return date('Y-m-d', $date);
        }
        return date('Y-m', $date);
    }

    /**
     * Gets order data
     * @param $start
     * @param $end
     * @param $n ( $n = 0: view by Day, $n = 1: view by Month
     * @param $companyId
     * @return array
     */

    protected function __getData($start, $end, $n, $companyId){
        if ($n == 0) {
            $startQuery = date("Y-m-d 00:00:00", strtotime($start));
            $endQuery = date("Y-m-d 23:59:59", strtotime($end));
        } else {
            $startQuery = date("Y-m-01 00:00:00", strtotime($start));
            $endQuery = date("Y-m-t 23:59:59", strtotime($end));
        }

        $company = Company::leftJoin('stores', 'stores.company_id', '=', 'companies.id')
            ->selectRaw('companies.*, count(stores.id) as storesCount')
            ->where('companies.id', $companyId)
            ->whereNull('stores.is_deleted')
            ->groupBy('companies.id')
            ->orderBy('created_at')->first()->toArray();

        $storeDeleted = Store::whereNotNull('is_deleted')->lists('id');

        $companyDeleted = Company::where('status', '!=', Company::STATUS_CANCELLATION_COMPLETED)->lists('id');

        $settings = Setting::where('company_id', $company['id'])->where('name', 'MediaidSettingCompany')->lists('value', 'key')->toArray();
        foreach ($settings as $k => $v) {
            $settings[$k] = json_decode($v, true);
        }

        $patientReplySettingMediaid = empty($settings['patientReplySettingMediaid']['used']) ? 0 : 1;
        $hotlineServiceMediaid = empty($settings['hotlineServiceMediaid']['used']) ? 0 : 1;
        $hotline24ServiceMediaid = empty($settings['hotline24ServiceMediaid']['used']) ? 0 : 1;
        $memberForMessageDeliveryMediaid = empty($settings['memberForMessageDeliveryMediaid']['used']) ? 0 : 1;

        $staffAdd = empty(json_decode($company['staff_add'])) ? 0 : json_decode($company['staff_add'])->number;
        $billableText = empty(json_decode($company['billable'])) ? '' : json_decode($company['billable'])->text;

        $viewOrderByCreated = Order::where('company_id', $companyId)
            ->whereNotIn('company_id', $companyDeleted)
            ->whereNotIn('store_id', $storeDeleted)
            ->where('created_at', '>=', new DateTime($startQuery))
            ->where('created_at', '<=', new DateTime($endQuery))
            ->orderBy('completed_at')->get()->toArray();

        $viewOrderByCompleted = Order::where('company_id', $companyId)
            ->whereNotIn('company_id', $companyDeleted)
            ->whereNotIn('store_id', $storeDeleted)
            ->where('completed_at', '>=', new DateTime($startQuery))
            ->where('completed_at', '<=', new DateTime($endQuery))
            ->orderBy('deleted_at')->get()->toArray();

        $viewOrderByDelete = Order::where('company_id', $companyId)
            ->whereNotIn('company_id', $companyDeleted)
            ->whereNotIn('store_id', $storeDeleted)
            ->where('deleted_at', '>=', new DateTime($startQuery))
            ->where('deleted_at', '<=', new DateTime($endQuery))
            ->orderBy('created_at')->get()->toArray();

        $viewUserByRegister = User::where('company_id', $companyId)
            ->whereNotIn('company_id', $companyDeleted)
            ->whereNotNull('created_at')
            ->where('created_at', '>=', new DateTime($startQuery))
            ->where('created_at', '<=', new DateTime($endQuery))
            ->orderBy('exited_at')->get()->toArray();

        $viewUserByExited = User::where('company_id', $companyId)
            ->whereNotIn('company_id', $companyDeleted)
            ->whereNotNull('exited_at')
            ->where('exited_at', '>=', new DateTime($startQuery))
            ->where('exited_at', '<=', new DateTime($endQuery));

        $viewUserAll = User::where('company_id', $companyId)
            ->whereNotIn('company_id', $companyDeleted)
            ->whereNotNull('created_at')
            ->where('created_at', '>=', new DateTime($startQuery))
            ->where('created_at', '<=', new DateTime($endQuery))
            ->orderBy('created_at')->get()->toArray();

        $totalUser = User::where('company_id', $companyId)
            ->whereNotIn('company_id', $companyDeleted)
            ->whereNotNull('created_at')
            ->where('created_at', '<', new DateTime($startQuery))->count();

        //group by date
        if ($n == 0) {
            $viewByTime = $this::__createDatesRangeArray($start, $end, 2);
        }
        else {
            $viewByTime = $this::__createMonthsRangeArray($start, $end, 2);
        }

        foreach ($viewByTime as $k => $v) {
            $viewByTime[$k]['id'] = $company['id'];
            $viewByTime[$k]['name'] = $company['name'];
            $viewByTime[$k]['viewTime'] = $n == 0 ? date('Y年m月d日', strtotime($v['time'])) :  date('Y年m月', strtotime($v['time']));
            //処方せん受信
            $viewByTime[$k]['viewAllOrderByCreated'] = 0;
            $viewByTime[$k]['viewOrderCompletedByCreated'] = 0;
            $viewByTime[$k]['viewUserDeletedByCreated'] = 0;
            $viewByTime[$k]['viewOrderByCompleted'] = 0;
            $viewByTime[$k]['viewOrderByDelete'] = 0;

            //会員数
            $viewByTime[$k]['viewUserByRegister'] = 0;
            $viewByTime[$k]['viewUserByExited'] = 0;
            $viewByTime[$k]['viewUserAll'] = 0;

            //企業別店舗数
            $viewByTime[$k]['countStore'] = $company['storesCount']; //契約店舗数
            $viewByTime[$k]['staffAdd'] = $staffAdd; //スタッフアカウント数（対象期間内追加分）

            //契約内容
            $viewByTime[$k]['billableText'] = $billableText; //基本契約（課金方法）
            $viewByTime[$k]['patientReplySettingMediaid'] = $patientReplySettingMediaid; //患者からの返信機能
            $viewByTime[$k]['memberForMessageDeliveryMediaid'] = $memberForMessageDeliveryMediaid; //会員向けメッセージ配信機能
            $viewByTime[$k]['hotlineServiceMediaid'] = $hotlineServiceMediaid; //ほっとラインサービス
            $viewByTime[$k]['hotline24ServiceMediaid'] = $hotline24ServiceMediaid; //ほっとライン24サービス

            foreach ($viewOrderByCreated as $s) {
                $id = $this::__groupBy($n, $s, 'created_at');
                if ($v['time'] == $id) {
                    $viewByTime[$k]['viewAllOrderByCreated'] += 1;
                }
                if ($v['time'] == $id && $s['completed_flag'] == 1) {
                    $viewByTime[$k]['viewOrderCompletedByCreated'] += 1;
                }
                if ($v['time'] == $id && $s['status'] == Order::STATUS_INVALID) {
                    $viewByTime[$k]['viewUserDeletedByCreated'] += 1;
                }
            }

            foreach ($viewOrderByCompleted as $s) {
                $id = $this::__groupBy($n, $s, 'completed_at');
                if ($v['time'] == $id) {
                    $viewByTime[$k]['viewOrderByCompleted'] += 1;
                }
            }

            foreach ($viewOrderByDelete as $s) {
                $id = $this::__groupBy($n, $s, 'deleted_at');
                if ($v['time'] == $id) {
                    $viewByTime[$k]['viewOrderByDelete'] += 1;
                }
            }

            foreach ($viewUserByRegister as $s) {
                $id = $this::__groupBy($n, $s, 'created_at');
                if ($v['time'] == $id) {
                    $viewByTime[$k]['viewUserByRegister'] += 1;
                }
            }

            foreach ($viewUserByExited as $s) {
                $id = $this::__groupBy($n, $s, 'exited_at');
                if ($v['time'] == $id) {
                    $viewByTime[$k]['viewUserByExited'] += 1;
                }
            }

            foreach ($viewUserAll as $s) {
                $id = $this::__groupBy($n, $s, 'created_at');
                if ($v['time'] == $id) {
                    $totalUser ++;
                    $viewByTime[$k]['viewUserAll'] += 1;
                }
                else {
                    $viewByTime[$k]['viewUserAll'] = $totalUser;
                }
            }
        }

        return $viewByTime;
    }


    /**
     * Gets order data
     * @param $start
     * @param $end
     * @param $n ( $n = 0: view by Day, $n = 1: view by Month
     * @param $statusOrder
     * @param $companyId
     * @return array
     */

    protected function __getDataOrders($start, $end, $n, $statusOrder, $companyId = NULL)
    {
        if ($n == 0) {
            $startQuery = date("Y-m-d 00:00:00", strtotime($start));
            $endQuery = date("Y-m-d 23:59:59", strtotime($end));
        } else {
            $startQuery = date("Y-m-01 00:00:00", strtotime($start));
            $endQuery = date("Y-m-t 23:59:59", strtotime($end));
        }

        $companyDeleted = Company::where('status', '!=', Company::STATUS_CANCELLATION_COMPLETED)->lists('id');

        switch ($statusOrder) {
            case Store::STATUS_INVALID :
                $groupBy = 'deleted_at';
                $count = 'invalidCount';
                break;

            case Store::STATUS_PREPARED :
                $groupBy = 'completed_at';
                $count = 'prepareCount';
                break;

            default:
                $groupBy = 'created_at';
                $count = 'requestCount';
                break;
        }

        $storeDeleted = Store::whereNotNull('is_deleted')->lists('id');

        if ($companyId) {
            $orders = Order::where('company_id', '=', $companyId)
                ->whereNotIn('company_id', $companyDeleted)
                ->whereNotIn('store_id', $storeDeleted)
                ->where($groupBy, '>=', new DateTime($startQuery))
                ->where($groupBy, '<=', new DateTime($endQuery))
                ->orderBy($groupBy)->get()->toArray();
        }

        else {
            $orders = Order::where($groupBy, '>=', new DateTime($startQuery))
                ->where($groupBy, '<=', new DateTime($endQuery))
                ->orderBy($groupBy)->get()->toArray();
        }

        //group by date
        if ($n == 0) {
            $viewByDays = $this::__createDatesRangeArray($start, $end);
            foreach ($orders as $s) {
                $date = strtotime($s[$groupBy]);

                $id = date('Y-m-d', $date);

                if (isset($viewByDays[$id])) {
                    $viewByDays[$id][] = $s;
                } else {
                    $viewByDays[$id] = array($s);
                }
            }
        }

        //group by month
        else {
            $viewByDays = $this::__createMonthsRangeArray($start, $end);
            foreach ($orders as $s) {
                $date = strtotime($s[$groupBy]);
                $id = date('Y-m', $date);

                if (isset($viewByDays[$id])) {
                    $viewByDays[$id][] = $s;
                } else {
                    $viewByDays[$id] = array($s);
                }
            }
        }

        $viewByDays['total'] = array();

        $viewByDays['total']['colour'] = '';

        if ( $statusOrder === Store::STATUS_CREATE_AT ) {
            $viewByDays['total']['dateOfWeek'] = '合計';
            $viewByDays['total']['fullDate'] = '合計';
            $viewByDays['total']['yearMonth'] = '合計';
            $viewByDays['total']['day'] = '合計';
            $viewByDays['total']['year'] = '合計';
            $viewByDays['total']['month'] = '合計';
            $viewByDays['total']['completedCount'] = 0;
            $viewByDays['total']['deletedCount'] = 0;

        }

        $viewByDays['total'][$count] = 0;


        foreach ($viewByDays as $key => $result) {
            if ($key != 'total') {
                $date = strtotime($key);
                switch (date('w', $date)) {
                    case 0:
                        $colour = 'pink';
                        break;
                    case 6:
                        $colour = 'blue';
                        break;
                    default:
                        $colour = '';
                }

                switch ($statusOrder) {
                    case Store::STATUS_CREATE_AT :
                        $viewByDays[$key] = array(
                            'dateOfWeek' => Store::$daysConvert[date('w', $date)],
                            'fullDate' => date('Y年m月d日', $date),
                            'yearMonth' => date('Y年m月', $date),
                            'year' => date('Y年', $date),
                            'month' => date('n', $date),
                            'day' => date('j', $date),
                            $count => 0,
                            'completedCount' => 0,
                            'deletedCount' => 0,
                            'colour' => $colour,
                        );
                        break;

                    default:
                        $viewByDays[$key] = array(
                            $count => 0,
                            'colour' => $colour,
                        );
                        break;
                }

                foreach ($result as $k => $r) {
                    $viewByDays[$key][$count] += 1;
                    if ($statusOrder === Store::STATUS_CREATE_AT) {
                        $viewByDays[$key]['completedCount'] += $r['completed_flag'] === 1 ? 1 : 0;
                        $viewByDays[$key]['deletedCount'] += $r['status'] === Order::STATUS_INVALID ? 1 : 0;
                    }

                    if (is_int($k)) {
                        unset($viewByDays[$key][$k]);
                    }
                }
                $viewByDays['total'][$count] += $viewByDays[$key][$count];

                if ($statusOrder === Store::STATUS_CREATE_AT) {
                    $viewByDays['total']['completedCount'] += $viewByDays[$key]['completedCount'];
                    $viewByDays['total']['deletedCount'] += $viewByDays[$key]['deletedCount'];
                }
                else {
                    unset($viewByDays['total']['completedCount']);
                    unset($viewByDays['total']['deletedCount']);
                }
            }
        }
        return $viewByDays;
    }

    /**
     * Gets order data
     * @param $start
     * @param $end
     * @param $n ( $n = 0: view by Day, $n = 1: view by Month
     * @param $statusUsers
     * @param $companyId
     * @return array
     */

    protected function __getDataUsers($start, $end, $n, $statusUsers, $companyId = NULL){
        if ($n == 0) {
            $startQuery = date("Y-m-d 00:00:00", strtotime($start));
            $endQuery = date("Y-m-d 23:59:59", strtotime($end));
        } else {
            $startQuery = date("Y-m-01 00:00:00", strtotime($start));
            $endQuery = date("Y-m-t 23:59:59", strtotime($end));
        }

        $groupBy = 'created_at';

        $totalUser = 0;

        switch ($statusUsers) {
            case 0 : //created_at group
                $users = User::whereNotNull($groupBy)
                    ->where($groupBy, '>=', new DateTime($startQuery))
                    ->where($groupBy, '<=', new DateTime($endQuery));

                if ($companyId) {
                    $users = $users->whereCompanyId($companyId);
                }

                $users = $users->orderBy($groupBy)->get()->toArray();
                break;

            case 1 : // exited_at group
                $groupBy = 'exited_at';
                $users = User::whereNotNull($groupBy)
                    ->where($groupBy, '>=', new DateTime($startQuery))
                    ->where($groupBy, '<=', new DateTime($endQuery));

                if ($companyId) {
                    $users = $users->whereCompanyId($companyId);
                }

                $users = $users->orderBy($groupBy)->get()->toArray();
                break;

            case 2: //all user created to end
                $users = User::whereNotNull($groupBy)
                    ->where($groupBy, '>=', new DateTime($startQuery))
                    ->where($groupBy, '<=', new DateTime($endQuery));

                $userExited = User::whereNotNull('exited_at')
                    ->where('exited_at', '>=', new DateTime($startQuery))
                    ->where('exited_at', '<=', new DateTime($endQuery));

                $totalUser = User::whereNotNull($groupBy)
                    ->where($groupBy, '<', new DateTime($startQuery));

                if($companyId) {
                    $users = $users->whereCompanyId($companyId);
                    $totalUser = $totalUser->whereCompanyId($companyId);
                    $userExited = $users->whereCompanyId($companyId);
                }

                $users = $users->orderBy($groupBy)->get()->toArray();
                $userExited = $userExited->orderBy('exited_at')->get()->toArray();
                $totalUser = $totalUser->count();

                break;
        }

        $total = $totalUser;

        $viewByTime = $n == 0 ? $this::__createDatesRangeArray($start, $end, 1) : $this::__createMonthsRangeArray($start, $end, 1);

        if (!empty($users)) {
            foreach ($users as $s) {
                $date = strtotime($s[$groupBy]);
                $id = $n == 0 ? date('Y-m-d', $date) : date('Y-m', $date);
                if (isset($viewByTime[$id])) $viewByTime[$id] ++;
            }
        }

        if ($statusUsers == 2) {
            if (!empty($userExited)) {
                foreach ($userExited as $s) {
                    $date = strtotime($s['exited_at']);
                    $id = $n == 0 ? date('Y-m-d', $date) : date('Y-m', $date);
                    $viewByTime[$id] --;
                }
            }

            $i = 0;
            $a = array();
            foreach ($viewByTime as $k => $v) {
                if ($i == 0) {
                    $viewByTime[$k] = $v + $total;
                }
                else {
                    $viewByTime[$k] =  $a[$i-1] + $v;
                }
                $a[$i] = $viewByTime[$k];
                $i++;
            }
        }

        $data = array();
        $totalCount = 0;

        foreach ($viewByTime as $key => $count) {
            $totalCount += $count;
            $data[$key]['count'] = $count;

            $date = strtotime($key);
            switch (date('w', $date)) {
                case 0:
                    $colour = 'pink';
                    break;
                case 6:
                    $colour = 'blue';
                    break;
                default:
                    $colour = '';
            }
            $data[$key]['colour'] = $colour;
        }

        $data['total']['count'] = $totalCount;
        $data['total']['colour'] = '';

        if ($statusUsers == 2) {
            $data['total']['count'] = '-';
        }

        return $data;
    }

    /**
     * @param $date (Y年m月d日)
     * @return  string standard date
     */
    protected function __parseDate($date)
    {
        if (empty($date)) {
            $date = date('Y-m-d', time());
        }
        $date = trim(preg_replace('/年|月|日/', '-', $date), '-');
        return $date;
    }

    /**
     * @param $date (Y-m-d)
     * @param $n
     * @return string japanese date
     */
    protected function __convertJapaneseDate($date, $n)
    {
        $strTime = strtotime($date);
        if ($n == 0) {
            return date('Y年m月', $strTime);
        }
        return date('Y年m月d日', $strTime);
    }

    /**
     * @param $strDateFrom (Y-m-d)
     * @param $strDateTo (Y-m-d)
     * @param $n = 1 (count only)
     * @return array of days between 2 param
     */
    protected function __createDatesRangeArray($strDateFrom, $strDateTo, $n = NULL)
    {
        $result = array();
        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom += 86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }

        if ($n == 2 ) {
            foreach ($aryRange as $r) {
                $result[]['time'] = $r;
            }
        }
        else if ($n == 1) {
            foreach ($aryRange as $r) {
                $result[$r] = 0;
            }
        }
        else {
            foreach ($aryRange as $r) {
                $result[$r] = array();
            }
        }
        return $result;
    }

    /**
     * @param $strDateFrom (Y-m-d)
     * @param $strDateTo (Y-m-d)
     * @param $n = 1 (count only)
     * @return array of months between 2 param
     */
    protected function __createMonthsRangeArray($strDateFrom, $strDateTo, $n = NULL)
    {
        $result = array();
        $start = (new DateTime($strDateFrom))->modify('first day of this month');
        $end = (new DateTime($strDateTo))->modify('last day of this month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        if ( $n == 2 ) {
            foreach ($period as $dt) {
                $result[]['time'] = $dt->format("Y-m");
            }
        }

        else if ($n == 1) {
            foreach ($period as $dt) {
                $result[$dt->format("Y-m")] = 0;
            }
        }
        else {
            foreach ($period as $dt) {
                $result[$dt->format("Y-m")] = array();
            }
        }
        return $result;

    }

    /**
     * Export CSV by Day
     */

    public function getDayCsv()
    {
        $startDate = !empty(session('statisticStartDate')) ? session('statisticStartDate') : date("Y-m-01", time());
        $endDate = !empty(session('statisticEndDate')) ? session('statisticEndDate') : date('Y-m-d', time());

        $viewByDaysCreatedAt = $this::__getDataOrders($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_CREATE_AT);
        $viewByDaysPrepared = $this::__getDataOrders($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_PREPARED);
        $viewByDaysInvalid = $this::__getDataOrders($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_INVALID);

        $viewByDaysUserRegister = $this::__getDataUsers($startDate, $endDate, Store::VIEW_BY_DAYS, 0);
        $viewByDaysUserExited = $this::__getDataUsers($startDate, $endDate, Store::VIEW_BY_DAYS, 1);
        $viewByDaysUserAll = $this::__getDataUsers($startDate, $endDate, Store::VIEW_BY_DAYS, 2);

        $fullDate = array_pluck($viewByDaysCreatedAt, 'fullDate');
        array_unshift($fullDate, '項目名');

        $requestCount = array_pluck($viewByDaysCreatedAt, 'requestCount');
        array_unshift($requestCount, '処方せん受信件数');

        $completedCount = array_pluck($viewByDaysCreatedAt, 'completedCount');
        array_unshift($completedCount, 'うち調剤完了件数');

        $deletedCount = array_pluck($viewByDaysCreatedAt, 'deletedCount');
        array_unshift($deletedCount, 'うち無効件数');

        $prepareCount = array_pluck($viewByDaysPrepared, 'prepareCount');
        array_unshift($prepareCount, '調剤完了件数');

        $invalidCount = array_pluck($viewByDaysInvalid, 'invalidCount');
        array_unshift($invalidCount, '無効件数');

        $allUserRegister = array_pluck($viewByDaysUserRegister, 'count');
        array_unshift($allUserRegister, '登録会員数');

        $allUserExited = array_pluck($viewByDaysUserExited, 'count');
        array_unshift($allUserExited, '退会者数');

        $allUsers = array_pluck($viewByDaysUserAll, 'count');
        array_unshift($allUsers, '累計会員数');

        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=data_statics_mediaid_".strtotime("now").".csv");

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));//utf-8 encoding

        fputcsv($handle, $fullDate);
        fputcsv($handle, $requestCount);
        fputcsv($handle, $completedCount);
        fputcsv($handle, $deletedCount);
        fputcsv($handle, $prepareCount);
        fputcsv($handle, $invalidCount);
        fputcsv($handle, $allUserRegister);
        fputcsv($handle, $allUserExited);
        fputcsv($handle, $allUsers);

        fclose($handle);
    }

    /**
     * Export CSV by Month
     */

    public function getMonthCsv()
    {
        $startMonth = !empty(session('statisticStartMonth')) ? session('statisticStartMonth') : date('Y-m-d', strtotime("-11 months"));
        $endMonth = !empty(session('statisticEndMonth')) ? session('statisticEndMonth') : date('Y-m-d', time());

        $viewByMonthsCreatedAt = $this::__getDataOrders($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_CREATE_AT);
        $viewByMonthsPrepared = $this::__getDataOrders($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_PREPARED);
        $viewByMonthsInvalid = $this::__getDataOrders($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_INVALID);

        $viewByMonthsUserRegister = $this::__getDataUsers($startMonth, $endMonth, Store::VIEW_BY_MONTH, 0);
        $viewByMonthsUserExited = $this::__getDataUsers($startMonth, $endMonth, Store::VIEW_BY_MONTH, 1);
        $viewByMonthsUserAll = $this::__getDataUsers($startMonth, $endMonth, Store::VIEW_BY_MONTH, 2);


        $yearMonth = array_pluck($viewByMonthsCreatedAt, 'yearMonth');
        array_unshift($yearMonth, '項目名');

        $requestCount= array_pluck($viewByMonthsCreatedAt, 'requestCount');
        array_unshift($requestCount, '処方せん受信件数');

        $completedCount = array_pluck($viewByMonthsCreatedAt, 'completedCount');
        array_unshift($completedCount, 'うち調剤完了件数');

        $deletedCount = array_pluck($viewByMonthsCreatedAt, 'deletedCount');
        array_unshift($deletedCount, 'うち無効件数');

        $prepareCount = array_pluck($viewByMonthsPrepared, 'prepareCount');
        array_unshift($prepareCount, '調剤完了件数');

        $invalidCount = array_pluck($viewByMonthsInvalid, 'invalidCount');
        array_unshift($invalidCount, '無効件数');

        $allUserRegister = array_pluck($viewByMonthsUserRegister, 'count');
        array_unshift($allUserRegister, '登録会員数');

        $allUserExited = array_pluck($viewByMonthsUserExited, 'count');
        array_unshift($allUserExited, '退会者数');

        $allUsers = array_pluck($viewByMonthsUserAll, 'count');
        array_unshift($allUsers, '累計会員数');

        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=data_statics_mediaid_".strtotime("now").".csv");

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));//utf-8 encoding


        fputcsv($handle, $yearMonth);
        fputcsv($handle, $requestCount);
        fputcsv($handle, $completedCount);
        fputcsv($handle, $deletedCount);
        fputcsv($handle, $prepareCount);
        fputcsv($handle, $invalidCount);
        fputcsv($handle, $allUserRegister);
        fputcsv($handle, $allUserExited);
        fputcsv($handle, $allUsers);

        fclose($handle);
    }

    /**
     * Gets searching input
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function postSearch(Request $request) {
        session(['monthlySummaryActive' => 1]);
        $input = $request->all();
        if (isset ($input['btn_reset']) ){
            session(['StoresSearchCsvData' => null]);
            return redirect()->to(action('Mediaid\MediaidsController@getIndex'));
        }

        session(['StoresSearchCsvData' => $input]);

        return redirect()->to(action('Mediaid\MediaidsController@getIndex'));
    }

    /**
     * Gets companies data
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function getCompaniesData($request) {
        $orderQuery = Company::where('status', '!=', Company::STATUS_CANCELLATION_COMPLETED)->select('id','name');

        $allCompany = $orderQuery->get();

        if (!empty ($request['company_name'])) {
            $orderQuery = $orderQuery->search('companies.name', $request['company_name']);
        }

        if (@$request['company_all'] === 'false') {
            return $allCompany;
        }
        return $orderQuery->get();
    }

    /**
     * Gets input for export csv
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function postDataCsv(Request $request) {
        session(['conditionMediaidDataCsv' => $request->all()]);
        return redirect()->to(action('Mediaid\MediaidsController@getConditionExportCsv'));
    }


    /**
     * Export csv with condition
     */
    public function getConditionExportCsv() {
        $con = session('conditionMediaidDataCsv');

        if ($con) {
            $listCompaniesId = @$con['company_id'] ? $con['company_id'] : '';
            $startDate = !empty($con['startDate']) ? $this::__parseDate($con['startDate']) : date("Y-m-01", time());
            $endDate = !empty($con['endDate']) ? $this::__parseDate($con['endDate']) : date('Y-m-d', time());
            $startMonth = !empty($con['startMonth']) ? $this::__parseDate($con['startMonth']) : date("Y-m-d", strtotime("-2 months"));
            $endMonth = !empty($con['endMonth']) ? $this::__parseDate($con['endMonth']) : date('Y-m-d', time());

            $companiesQuery = DB::table('companies')->select('id')->whereIn('companies.id', $listCompaniesId)->get();

            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header("Content-Disposition: attachment; filename=data_mediaid_".strtotime("now").".csv");

            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));//utf-8 encoding

            if ($companiesQuery) {
                $title = [
                    'id' => '企業コード',
                    'name' => '企業名',
                    'viewTime' => '日付',
                    'viewAllOrderByCreated' => '処方せん受信件数',
                    'viewOrderCompletedByCreated' => '処方せん受信件数うち調剤完了件数',
                    'viewUserDeletedByCreated' => '処方せん受信件数うち無効件数',
                    'viewOrderByCompleted' => '調剤完了件数',
                    'viewOrderByDelete' => '無効件数',
                    'viewUserByRegister' => '登録会員数',
                    'viewUserByExited' => '退会者数',
                    'viewUserAll' => '累計会員数',
                    'countStore' => '契約店舗数',
                    'staffAdd' => 'スタッフアカウント数（追加）',
                    'billableText' => '基本契約（課金対象）',
                    'patientReplySettingMediaid' => '患者からの返信機能',
                    'memberForMessageDeliveryMediaid' => '会員向けメッセージ配信機能',
                    'hotlineServiceMediaid' => 'ほっとラインサービス',
                    'hotline24ServiceMediaid' => 'ほっとライン24サービス',
                ];

                $conCsv = $con;
                $conCsv['name'] = $conCsv['time'] = $conCsv['id'] = $conCsv['viewTime'] = '';
                unset($conCsv['_token'], $conCsv['startDate'], $conCsv['endDate'], $conCsv['company_id'], $conCsv['optionsRadios'], $conCsv['time']);

                $title = array_values(array_intersect_key($title, $conCsv));
                fputcsv($handle, $title);

                if (isset($con['optionsRadios'])) {
                    foreach ($listCompaniesId as $id) {
                        $content = [];

                        $data = $con['optionsRadios'] == 0 ? $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, $id) : $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, $id);

                        foreach($data as $key => $d) {
                            $content[$key] = array_intersect_key($d, $conCsv);
                        }
                        foreach($content as $c) {
                            fputcsv($handle, $c);
                        }
                    }
                }
            }
            fclose($handle);
        }
    }

}