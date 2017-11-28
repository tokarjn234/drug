<?php


namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use DateTime;
use DateInterval;
use DatePeriod;
use Symfony\Component\HttpFoundation\File;
use Intervention\Image\Facades\Image;
use Validator;
use App\Models\Order;
use App\Models\City;
use App\Models\Setting;
use App\Models\Province;
use App\Models\Store;

class StoresController extends HomeAppController
{
    /**
     * Stores Statistic list
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
            } else if (isset($inputs['startMonth']) && isset($inputs['endMonth'])) {
                $dayActive = "";
                $monthActive = "active in";
                $currentActive = "";
            } else {
                $dayActive = "";
                $monthActive = "";
                $currentActive = "active in";
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

        session(['statisticInputs' => '']);

        return view("home.stores.index", compact(
            'viewByDaysCreatedAt', 'viewByMonthsCreatedAt', 'viewByDaysPrepared', 'viewByMonthsPrepared', 'viewByDaysInvalid', 'viewByMonthsInvalid',
            'dayActive', 'currentActive', 'monthActive', 'viewByCurrent', 'viewByCurrentPrepared', 'viewByCurrentInvalid',
            'startDate', 'endDate', 'startMonth', 'endMonth'));
    }


    /**
     * Stores Statistic list
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function postIndex(Request $request)
    {
        session(['statisticInputs' => $request->all()]);

        return redirect('stores');

    }

    /**
     * Gets order data
     * @param $start
     * @param $end
     * @param $n
     * @param $status
     * @return array
     */

    protected function __getData($start, $end, $n, $status)
    {
        $storeId = $this->getCurrentStore('id');

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

        $statistic = Order::where('store_id', '=', $storeId)
            ->where($groupBy, '>=', new DateTime($startQuery))
            ->where($groupBy, '<=', new DateTime($endQuery))
            ->orderBy($groupBy)->get();

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
        } //group by month
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

        if ($status === Store::STATUS_CREATE_AT) {
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
                } else {
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
        header("Content-Disposition: attachment; filename=data_statics_stores_" . strtotime("now") . ".csv");

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));//utf-8 encoding

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
        header("Content-Disposition: attachment; filename=data_statics_stores_" . strtotime("now") . ".csv");

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));//utf-8 encoding


        fputcsv($handle, $yearMonth);
        fputcsv($handle, $requestCount);
        fputcsv($handle, $completedCount);
        fputcsv($handle, $deletedCount);
        fputcsv($handle, $prepareCount);
        fputcsv($handle, $invalidCount);

        fclose($handle);
    }

    /**
     * Shows current store profile
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getShow()
    {
        $storeSetting = Store::getStoreInputSetting();
        $isEditable = false;

        foreach ($storeSetting as $key => $options) {
            if ($options['edit']) {
                $isEditable = true;
                break;
            }
        }


        $store = Store::where('id', $this->getCurrentStore('id'))->first();

        if (empty($store)) {
            return view('errors.404');
        } else {
            return view('home.stores.show', ['store' => $store->toArray(), 'storeSetting' => $storeSetting, 'isEditable' => $isEditable]);
        }
    }

    /**
     * Edit current store
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEdit()
    {

        $store = Store::where('id', $this->getCurrentStore('id'))->first();


        if (empty($store)) {
            return view('errors.404');
        } else {
            $provinces = Province::lists('name', 'name')->toArray();

            $cities = City::select('cities_master.id', 'cities_master.name')->join('province_master', 'cities_master.province_id', '=', 'province_master.id')
                ->where('province_master.name', '=', $store->province)->get()->toArray();

            //Store Address
            $provinceName = $store->province;
            $issetProvince = Province::where('name', $provinceName)->first();
            $issetCity = City::where('cities_master.name', $store->city1)
                ->join('province_master', 'province_master.id', '=', 'cities_master.province_id')
                ->where(function ($query) use ($provinceName) {
                    $query->where('province_master.name', '=', $provinceName);
                })
                ->first();
            //
            $allowAdd['province'] = '';
            $allowAdd['city'] = '';
            if (empty($issetProvince)) {
                $allowAdd['province'] = $provinceName;
                if (empty($issetCity)) {
                    $allowAdd['city'] = $store->city1;
                }
            }


            return view('home.stores.edit', [
                'store' => $store->toArray(),
                'storeSetting' => Store::getStoreInputSetting(),
                'provinces' => $provinces,
                'jsonData' => [
                    'store' => $store->toArray(),
                    'cities' => $cities,
                    'provinces' => $provinces,
                    'getCitiesListUrl' => action('Home\StoresController@getCitiesList'),
                    'allowAdd' => $allowAdd
                ]
            ]);
        }
    }

    /**
     * Gets all cities of given province name
     * @param Request $request
     * @return array
     */
    public function getCitiesList(Request $request)
    {
        $cities = City::select('cities_master.id', 'cities_master.name')->join('province_master', 'cities_master.province_id', '=', 'province_master.id')
            ->where('province_master.name', '=', $request->input('province'))->get()->toArray();


        return r_ok($cities);
    }

    /**
     * Updates current store
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function postUpdate(Request $request)
    {

        /* $validator = Validator::make($request->all(), array(
             'name' => 'required|max:30',
             'province' => 'required',
             'city1' => 'required',
             'address' => 'required',
             'phone_number' => 'required',
             'accept_credit_card' => 'required',
             'postal_code[0]' => 'alpha_num',
             'postal_code[1]' => 'alpha_num',
             'fax_number[0]' => 'numeric',
             'fax_number[1]' => 'numeric',
             'fax_number[2]' => 'numeric'
         ));


         if ($validator->fails()) {
             return redirect('stores/edit')->with('errors', $validator->errors())->withInput();
         }*/

        $store = Store::where('id', $this->getCurrentStore('id'))->first();
        $checkDuplicateCode = $this->checkStoreCodeDuplicate($request['internal_code'], $store['alias'], true);

        if ($checkDuplicateCode) {
            return redirect('stores/edit')->withErrors(['StoreIdDuplicated' => __('StoreID already exists')])
                ->withInput(['internal_code' => $request['internal_code']]);
        }

        $fields = ['internal_code', 'name', 'province', 'city1', 'address', 'accept_credit_card', 'credit_card_type', 'park_info', 'description', 'map_coordinates_lat', 'map_coordinates_long'];

        foreach ($fields as $field) {
            if (isset ($request[$field])) {
                $data[$field] = $request[$field];
            }
        }

        if (isset ($request['postal_code'])) {
            $data['postal_code'] = implode('-', $request['postal_code']);
        }

        if (isset ($request['phone_number'])) {
            $data['phone_number'] = implode('-', $request['phone_number']);
        }

        if (isset ($request['fax_number'])) {
            $data['fax_number'] = implode('-', $request['fax_number']);
        }

        $data['update_staff_id'] = $this->getCurrentStaff('id');

        $workingTime = [];

        if (isset ($request['times_open']) || isset ($request['times_close'])) {
            foreach (Store::$days as $key => $value) {
                $openTime = isset($request['times_open'][$key]) ? $request['times_open'][$key] : '休';
                $closeTime = isset($request['times_close'][$key]) ? $request['times_close'][$key] : '休';
                $workingTime['data'][] = array(
                    'id' => $key,
                    'title' => $value,
                    'start' => $openTime == '休' ? '休' : $request['times_open'][$key],
                    'end' => $closeTime == '休' ? '休' : $request['times_close'][$key]
                );
            }

        }

        $workingTime['note'] = '';
        if (isset ($request['note'])) {
            $workingTime['note'] = $request['note'];
        }

        if (!empty ($workingTime)) {
            $data['working_time'] = json_encode($workingTime);
        }

        if (!file_exists(public_path('images/stores'))) {
            mkdir(public_path('images/stores'), 0777, true);
        }

        $image = $request->file('photo_url');


        if (!empty($image)) {
            $filename = 'StoreImage' . time() . '.' . $image->getClientOriginalExtension();
            $path = 'images/stores/' . $filename;
            $data['photo_url'] = $path;

            move_uploaded_file($image->getRealPath(), $path);
            @unlink(public_path($store->photo_url));//unlink old password
        }

        Store::where('id', $this->getCurrentStore('id'))->update($data);

        return redirect('stores/show')->with('status', 'Successfully');
    }

    /**
     * check duplicate storeID
     * @param Request $request
     * @return boolean
     */
    private function checkStoreCodeDuplicate($storeCode, $storeAlias = null, $edit = false)
    {
        $companyId = $this->getCurrentCompany('id');

        if ($edit) {
            $store = Store::whereInternalCode($storeCode)
                ->whereCompanyId($companyId)
                ->where('alias', '!=', $storeAlias)
                ->count();
        } else {
            $store = Store::whereInternalCode($storeCode)
                ->whereCompanyId($companyId)
                ->count();
        }

        return $store > 0;
    }
}