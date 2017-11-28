<?php


namespace App\Http\Controllers\Company;

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



class CompaniesController extends CompanyAppController
{
    /**
     * Staffs Statistic list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function getIndex()
    {
        $ListProvinceCities = Province::join('cities_master', 'province_master.id', '=', 'cities_master.province_id')
            ->select('cities_master.name AS cities_name', 'cities_master.id AS cities_id', 'province_master.id AS province_id')->get()->toArray();

        $cityListRelation = array();

        foreach ($ListProvinceCities as $key => $s) {
            $id = $s['province_id'];

            if (isset($cityListRelation[$id])) {
                $cityListRelation[$id][$s['cities_id']] = $s['cities_name'];
            } else {
                $cityListRelation[$id] = array();
                $cityListRelation[$id][0] = '';
                $cityListRelation[$id][$s['cities_id']] = $s['cities_name'];
            }
        }

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

        $viewByDaysCreatedAt = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_CREATE_AT);
        $viewByMonthsCreatedAt = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_CREATE_AT);
        $viewByDaysPrepared = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_PREPARED);
        $viewByMonthsPrepared = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_PREPARED);
        $viewByDaysInvalid = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_INVALID);
        $viewByMonthsInvalid = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_INVALID);

        $viewByCurrent = $this::__getData($yesterday, $currentDate, Store::VIEW_BY_DAYS, Store::STATUS_CREATE_AT);
        $viewByCurrentPrepared = $this::__getData($yesterday, $currentDate, Store::VIEW_BY_DAYS, Store::STATUS_PREPARED);
        $viewByCurrentInvalid = $this::__getData($yesterday, $currentDate, Store::VIEW_BY_DAYS, Store::STATUS_INVALID);

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
        $startDate = $this::__convertJapaneseDate($startDate, 1);
        $endDate = $this::__convertJapaneseDate($endDate, 1);
        $startMonth = $this::__convertJapaneseDate($startMonth, 0);
        $endMonth = $this::__convertJapaneseDate($endMonth, 0);

        $companyId = $this->getCurrentCompany('id');
        $stores  = Store::where('company_id', '=', $companyId)->whereNull('is_deleted')->get();

        $cities = DB::table('cities_master')->lists('name', 'id');
        $cities = [0 => ''] + $cities;
        $provinces = DB::table('province_master')->lists('name', 'id');
        $provinces = [0 => ''] + $provinces;

        $search = session('StoresSearchCsvData');
        if ($search && empty($inputs)) {
            $stores = $this->getStoresData($search);
        }

        if (session('monthlySummaryActive') && empty($inputs)) {
            $dayActive = "";
            $monthActive = "";
            $currentActive = "";
            $monthlySummaryActive = "active in";
        }

        session(['monthlySummaryActive' => '']);

        session(['statisticInputs' => '']);

        return view("company.companies.index", compact(
            'viewByDaysCreatedAt', 'viewByMonthsCreatedAt', 'viewByDaysPrepared', 'viewByMonthsPrepared', 'viewByDaysInvalid', 'viewByMonthsInvalid',
            'dayActive', 'currentActive', 'monthActive', 'monthlySummaryActive', 'viewByCurrent', 'viewByCurrentPrepared', 'viewByCurrentInvalid',
            'startDate', 'endDate', 'startMonth', 'endMonth', 'stores', 'provinces', 'cities', 'search', 'cityListRelation'
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
        return redirect('company/companies');

    }

    /**
     * Gets order data
     * @param $start
     * @param $end
     * @param $n ( $n = 0: view by Day, $n = 1: view by Month
     * @param $status
     * @param $storeId
     * @return array
     */

    protected function __getData($start, $end, $n, $status, $storeId = NULL)
    {
        $companyId = $this->getCurrentCompany('id');

        if ($n == 0) {
            $startQuery = date("Y-m-d 00:00:00", strtotime($start));
            $endQuery = date("Y-m-d 23:59:59", strtotime($end));
        } else {
            $startQuery = date("Y-m-01 00:00:00", strtotime($start));
            $endQuery = date("Y-m-t 23:59:59", strtotime($end));
        }

        switch ($status) {
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

        if ($storeId) {
            $statistic = Order::where('store_id', '=', $storeId)
                ->where($groupBy, '>=', new DateTime($startQuery))
                ->where($groupBy, '<=', new DateTime($endQuery))
                ->orderBy($groupBy)->get();
        }

        else {
            $statistic = Order::where('company_id', '=', $companyId)
                ->where($groupBy, '>=', new DateTime($startQuery))
                ->where($groupBy, '<=', new DateTime($endQuery))
                ->orderBy($groupBy)->get();
        }

        //group by date
        if ($n == 0) {
            $viewByDays = $this::__createDatesRangeArray($start, $end);
            foreach ($statistic->toArray() as $s) {
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
            foreach ($statistic as $s) {
                $date = strtotime($s->$groupBy);
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

        if ( $status === Store::STATUS_CREATE_AT ) {
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

                switch ($status) {
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
                    if ($status === Store::STATUS_CREATE_AT) {
                        $viewByDays[$key]['completedCount'] += $r['completed_flag'] === 1 ? 1 : 0;
                        $viewByDays[$key]['deletedCount'] += $r['status'] === Order::STATUS_INVALID ? 1 : 0;
                    }

                    if (is_int($k)) {
                        unset($viewByDays[$key][$k]);
                    }
                }
                $viewByDays['total'][$count] += $viewByDays[$key][$count];

                if ($status === Store::STATUS_CREATE_AT) {
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
     * @return array of days between 2 param
     */
    protected function __createDatesRangeArray($strDateFrom, $strDateTo)
    {
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

        $results = array();
        foreach ($aryRange as $r) {
            $results[$r] = array();
        }
        return $results;
    }

    /**
     * @param $strDateFrom (Y-m-d)
     * @param $strDateTo (Y-m-d)
     * @return array of months between 2 param
     */
    protected function __createMonthsRangeArray($strDateFrom, $strDateTo)
    {
        $result = array();
        $start = (new DateTime($strDateFrom))->modify('first day of this month');
        $end = (new DateTime($strDateTo))->modify('last day of this month');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $result[$dt->format("Y-m")] = array();
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

        $viewByDaysCreatedAt = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_CREATE_AT);
        $viewByDaysPrepared = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_PREPARED);
        $viewByDaysInvalid = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_INVALID);

        $result = array_merge_recursive($viewByDaysCreatedAt, $viewByDaysPrepared, $viewByDaysInvalid);

        $fullDate = array_pluck($result, 'fullDate');
        array_unshift($fullDate, '項目名');

        $requestCount = array_pluck($result, 'requestCount');
        array_unshift($requestCount, '処方せん受信件数');

        $completedCount = array_pluck($result, 'completedCount');
        array_unshift($completedCount, 'うち調剤完了件数');

        $deletedCount = array_pluck($result, 'deletedCount');
        array_unshift($deletedCount, 'うち無効件数');

        $prepareCount = array_pluck($result, 'prepareCount');
        array_unshift($prepareCount, '調剤完了件数');

        $invalidCount = array_pluck($result, 'invalidCount');
        array_unshift($invalidCount, '無効件数');

        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=data_statics_stores_".strtotime("now").".csv");

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));//utf-8 encoding

        fputcsv($handle, $fullDate);
        fputcsv($handle, $requestCount);
        fputcsv($handle, $completedCount);
        fputcsv($handle, $deletedCount);
        fputcsv($handle, $prepareCount);
        fputcsv($handle, $invalidCount);

        fclose($handle);
    }

    /**
     * Export CSV by Month
     */

    public function getMonthCsv()
    {
        $startMonth = !empty(session('statisticStartMonth')) ? session('statisticStartMonth') : date('Y-m-d', strtotime("-11 months"));
        $endMonth = !empty(session('statisticEndMonth')) ? session('statisticEndMonth') : date('Y-m-d', time());
        $viewByMonthsCreatedAt = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_CREATE_AT);
        $viewByMonthsPrepared = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_PREPARED);
        $viewByMonthsInvalid = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_INVALID);

        $result = array_merge_recursive($viewByMonthsCreatedAt, $viewByMonthsPrepared, $viewByMonthsInvalid);

        $yearMonth = array_pluck($result, 'yearMonth');
        array_unshift($yearMonth, '項目名');

        $requestCount= array_pluck($result, 'requestCount');
        array_unshift($requestCount, '処方せん受信件数');

        $completedCount = array_pluck($result, 'completedCount');
        array_unshift($completedCount, 'うち調剤完了件数');

        $deletedCount = array_pluck($result, 'deletedCount');
        array_unshift($deletedCount, 'うち無効件数');

        $prepareCount = array_pluck($result, 'prepareCount');
        array_unshift($prepareCount, '調剤完了件数');

        $invalidCount = array_pluck($result, 'invalidCount');
        array_unshift($invalidCount, '無効件数');

        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=data_statics_stores_".strtotime("now").".csv");

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));//utf-8 encoding


        fputcsv($handle, $yearMonth);
        fputcsv($handle, $requestCount);
        fputcsv($handle, $completedCount);
        fputcsv($handle, $deletedCount);
        fputcsv($handle, $prepareCount);
        fputcsv($handle, $invalidCount);

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
            return redirect()->to(action('Company\CompaniesController@getIndex'));
        }

        session(['StoresSearchCsvData' => $input]);

        return redirect()->to(action('Company\CompaniesController@getIndex'));
    }

    /**
     * Gets stores data
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function getStoresData($request) {
        $companyId = $this->getCurrentCompany('id');
        $orderQuery = Store::where('company_id', '=', $companyId)->whereNull('is_deleted')
        ->select('id','name','internal_code');

        $cities = DB::table('cities_master')->lists('name', 'id');

        $cities = [0 => ''] + $cities;
        $provinces = DB::table('province_master')->lists('name', 'id');
        $provinces = [0 => ''] + $provinces;

        if (!empty ($request['province']) && $request['province'] !== '0') {
            $orderQuery = $orderQuery->where('stores.province', '=', $provinces[$request['province']]);
        }

        if (!empty ($request['city1'])) {
            $orderQuery = $orderQuery->where('stores.city1', '=', [$cities[$request['city1']]]);
        }

        if (!empty ($request['store_name'])) {
            $orderQuery = $orderQuery->search('stores.name', $request['store_name']);
        }

        if (@$request['store_all'] === 'false') {
            $orderQuery = Store::whereNull('is_deleted')->select('id','name','internal_code')->get();
            return $orderQuery;
        }
        return $orderQuery->get();
    }

    /**
     * Gets input for export csv
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function postDataCsv(Request $request) {
        session(['conditionDataCsv' => $request->all()]);
        return redirect()->to(action('Company\CompaniesController@getConditionExportCsv'));
    }


    /**
     * Export csv with condition
     */
    public function getConditionExportCsv() {
        $con = session('conditionDataCsv');

        if ($con) {
            $listsId = @$con['store_id'] ? $con['store_id'] : '';
            $startDate = !empty($con['startDate']) ? $this::__parseDate($con['startDate']) : date("Y-m-01", time());
            $endDate = !empty($con['endDate']) ? $this::__parseDate($con['endDate']) : date('Y-m-d', time());
            $startMonth = !empty($con['startMonth']) ? $this::__parseDate($con['startMonth']) : date("Y-m-d", strtotime("-2 months"));
            $endMonth = !empty($con['endMonth']) ? $this::__parseDate($con['endMonth']) : date('Y-m-d', time());

            $storesQuery = DB::table('stores')->select('id','name','internal_code')->whereIn('stores.id', $listsId)->get();

            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Cache-Control: private', false);
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header("Content-Disposition: attachment; filename=data_statics_stores_".strtotime("now").".csv");

            $handle = fopen('php://output', 'w');

            //dates
            if ($storesQuery) {
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));//utf-8 encoding

                if (isset($con['optionsRadios']) && $con['optionsRadios'] == 0) {

                    foreach ($storesQuery as $key => $s) {
                        $viewByDaysCreatedAt = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_CREATE_AT, $s->id );
                        $viewByDaysPrepared = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_PREPARED, $s->id);
                        $viewByDaysInvalid = $this::__getData($startDate, $endDate, Store::VIEW_BY_DAYS, Store::STATUS_INVALID, $s->id);
                        $result = array_merge_recursive($viewByDaysCreatedAt, $viewByDaysPrepared, $viewByDaysInvalid);

                        if ($key == 0) {
                            $fullDate = array_pluck($result, 'fullDate');
                            array_unshift($fullDate, '項目名');
                            array_unshift($fullDate, '店舗名');
                            array_unshift($fullDate, '店舗コード');

                            fputcsv($handle, $fullDate);
                        }

                        if (isset($con['checkCreated'])) {
                            $requestCount = array_pluck($result, 'requestCount');
                            array_unshift($requestCount, '処方せん受信件数');
                            array_unshift($requestCount, $s->name);
                            array_unshift($requestCount, $s->id);
                            fputcsv($handle, $requestCount);
                        }

                        if (isset($con['checkCreatedCompleted'])) {
                            $completedCount = array_pluck($result, 'completedCount');
                            array_unshift($completedCount, '処方せん受信件数うち調剤完了件数');
                            array_unshift($completedCount, $s->name);
                            array_unshift($completedCount, $s->id);
                            fputcsv($handle, $completedCount);
                        }

                        if (isset($con['checkCreatedDeleted'])) {
                            $deletedCount = array_pluck($result, 'deletedCount');
                            array_unshift($deletedCount, '処方せん受信件数うち無効件数');
                            array_unshift($deletedCount, $s->name);
                            array_unshift($deletedCount, $s->id);
                            fputcsv($handle, $deletedCount);
                        }

                        if (isset($con['checkCompleted'])) {
                            $prepareCount = array_pluck($result, 'prepareCount');
                            array_unshift($prepareCount, '調剤完了件数');
                            array_unshift($prepareCount, $s->name);
                            array_unshift($prepareCount, $s->id);
                            fputcsv($handle, $prepareCount);
                        }

                        if (isset($con['checkDeleted'])) {
                            $invalidCount = array_pluck($result, 'invalidCount');
                            array_unshift($invalidCount, '無効件数');
                            array_unshift($invalidCount, $s->name);
                            array_unshift($invalidCount, $s->id);
                            fputcsv($handle, $invalidCount);
                        }
                    }
                }
                //months
                if (isset($con['optionsRadios']) && $con['optionsRadios'] == 1) {

                    foreach ($storesQuery as $key => $s) {
                        $viewByMonthsCreatedAt = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_CREATE_AT, $s->id);
                        $viewByMonthsPrepared = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_PREPARED, $s->id);
                        $viewByMonthsInvalid = $this::__getData($startMonth, $endMonth, Store::VIEW_BY_MONTH, Store::STATUS_INVALID, $s->id);

                        $result = array_merge_recursive($viewByMonthsCreatedAt, $viewByMonthsPrepared, $viewByMonthsInvalid);

                        if ($key == 0) {
                            $yearMonth = array_pluck($result, 'yearMonth');
                            array_unshift($yearMonth, '項目名');
                            array_unshift($yearMonth, '店舗名');
                            array_unshift($yearMonth, '店舗コード'); 
                            fputcsv($handle, $yearMonth);
                        }

                        if (isset($con['checkCreated'])) {
                            $requestCount = array_pluck($result, 'requestCount');
                            array_unshift($requestCount, '処方せん受信件数');
                            array_unshift($requestCount, $s->name);
                            array_unshift($requestCount, $s->id);
                            fputcsv($handle, $requestCount);
                        }

                        if (isset($con['checkCreatedCompleted'])) {
                            $completedCount = array_pluck($result, 'completedCount');
                            array_unshift($completedCount, '処方せん受信件数うち調剤完了件数');
                            array_unshift($completedCount, $s->name);
                            array_unshift($completedCount, $s->id);
                            fputcsv($handle, $completedCount);
                        }

                        if (isset($con['checkCreatedDeleted'])) {
                            $deletedCount = array_pluck($result, 'deletedCount');
                            array_unshift($deletedCount, '処方せん受信件数うち無効件数');
                            array_unshift($deletedCount, $s->name);
                            array_unshift($deletedCount, $s->id);
                            fputcsv($handle, $deletedCount);
                        }

                        if (isset($con['checkCompleted'])) {
                            $prepareCount = array_pluck($result, 'prepareCount');
                            array_unshift($prepareCount, '調剤完了件数');
                            array_unshift($prepareCount, $s->name);
                            array_unshift($prepareCount, $s->id);
                            fputcsv($handle, $prepareCount);
                        }

                        if (isset($con['checkDeleted'])) {
                            $invalidCount = array_pluck($result, 'invalidCount');
                            array_unshift($invalidCount, '無効件数');
                            array_unshift($invalidCount, $s->name);
                            array_unshift($invalidCount, $s->id);
                            fputcsv($handle, $invalidCount);
                        }

                    }
                }
                fclose($handle);
            }
        }
    }

}