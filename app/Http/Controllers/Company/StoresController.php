<?php

namespace App\Http\Controllers\Company;

use App\Models\Setting;
use Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use App\Models\Company;
use App\Models\Province;
use App\Models\City;
use Validator;
use Illuminate\Http\Request;
use DB;
use Hash;

class StoresController extends CompanyAppController
{
    private $requiredWorkingTime;
    private $isCreditCardType;
    private $action;

    /**
     * Gets users data
     * @param $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex(Request $request)
    {
        $companyId = $this::getCurrentCompany('id');
        $contractStore = $this->getCurrentCompany('contract_store');

        $storesQuery = Store::where('company_id', '=', $companyId)->orderBy('created_at', 'desc');
        $paginate = $storesQuery->paginate(10);
        $stores = Store::render($paginate);
        $numberStore = count(Store::where('company_id', '=', $companyId)->whereNull('is_deleted')->get());
        $enableCreate = $contractStore > $numberStore ? true : false;

        return view('company.stores.index', [
            'enableCreate' => $enableCreate,
            'paginate' => $paginate,
            'jsonData' => [
                'stores' => $stores,
                'msgUrl' => action('Home\MessagesController@getIndex', ['ordering' => 1, 'page' => $paginate->toArray()['current_page']]),
                'publicStoreUrl' => action('Company\StoresController@postPublicStatus'),
                'changePhotoUrl' => action('Company\StoresController@postChangePhoto'),
                'deletePhotoUrl' => action('Company\StoresController@postDeletePhotoUrl')
            ]
        ]);
    }

    public function postPublicStatus(Request $request)
    {
        $id = $request['store_id'];
        $is_published = $request['is_public'];
        $result = '';
        $contractStore = $this->getCurrentCompany('contract_store');
        $storePublic = Store::where('company_id', $this->getCurrentCompany('id'))->where('is_published', 1)->whereNull('is_deleted')->count();
        $enableCreate = $contractStore > $storePublic ? true : false;

        if (in_array($id, Company::getListStores())) {
            if ($is_published == 1) {
                $result = Store::whereId($id)->update(['is_published' => 0]);
            } else {
                if ($enableCreate) {
                    $result = Store::whereId($id)->update(['is_published' => 1]);
                } else {
                    return r_err('Not public Store!');
                }
            }
        }
        return $result ? r_ok($result) : r_err('Something went wrong!');

    }

    public function postChangePhoto(Request $request)
    {
        $image = $request['file'];
        $result = '';

        if (in_array($request["store_id"], Company::getListStores())) {

            if (!file_exists(public_path('images/stores'))) {
                mkdir(public_path('images/stores'), 0777, true);
            }
            $filename = 'StoreImage' . time() . '.' . $image->getClientOriginalExtension();
            $path = 'images/stores/' . $filename;
            @unlink(public_path($request["photo_url"]));
            move_uploaded_file($image->getPathName(), $path);

            $result = Store::whereId($request["store_id"])->update(['photo_url' => $path]);
        }
        return $result ? r_ok($path) : r_err('Something went wrong!');
    }

    public function postDeletePhotoUrl(Request $request)
    {
        $id = $request['store_id'];
        $result = '';

        if (in_array($id, Company::getListStores())) {
            $result = Store::where('id', $id)->update(['photo_url' => '']);
            @unlink(public_path($request['photo_url']));
        }
        return $result ? r_ok($result) : r_err('Something went wrong!');
    }

    /**
     * Gets csv data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCsv()
    {
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=data_stores_" . strtotime("now") . ".csv");

        $handle = fopen('php://output', 'w');
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));//utf-8 encoding

        $companyId = $this->getCurrentCompany('id');
        $storeLists = Store::whereCompanyId($companyId)->get();

        $day = ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su', 'Holiday'];

        foreach ($storeLists as $key => $store) {
            $dayI = 0;
            foreach (json_decode($store->working_time)->data as $times) {
                $storeLists[$key][$day[$dayI] . 'Start'] = $times->start;
                $storeLists[$key][$day[$dayI] . 'End'] = $times->end;
                $dayI++;
            }
            $storeLists[$key]['note'] = !empty(json_decode($store->working_time)->note) ? json_decode($store->working_time)->note : '';
        }

        fputcsv($handle, array(
            __('NewUpdated'),
            __('StoreID'),
            __('StoreName'),
            __('PostalCode'),
            __('Prefectures'),
            __('City'),
            __('AddressFull'),
            __('PhoneNumber'),
            __('FaxNumber'),
            __('StartMonday'),
            __('EndMonday'),
            __('StartTuesday'),
            __('EndTuesday'),
            __('StartWednesday'),
            __('EndWednesday'),
            __('StartThursday'),
            __('EndThursday'),
            __('StartFriday'),
            __('EndFriday'),
            __('StartSaturday'),
            __('EndSaturday'),
            __('StartSunday'),
            __('EndSunday'),
            __('StartHoliday'),
            __('EndHoliday'),
            __('Comment'),
            __('CreditCardIsUse'),
            __('CreditCardType'),
            __('Parking'),
            __('NewFromTheStore'),
            __('PublicOrPrivate')
        ));

        foreach ($storeLists as $store) {
            fputcsv($handle, array(
                strtotime($store->created_at) === strtotime($store->updated_at) ? '新規' : '更新',
                $store->internal_code,
                $store->name,
                $store->postal_code,
                $store->province,
                $store->city1,
                $store->address,
                $store->phone_number,
                $store->fax_number,
                $store->MoStart,
                $store->MoEnd,
                $store->TuStart,
                $store->TuEnd,
                $store->WeStart,
                $store->WeEnd,
                $store->ThStart,
                $store->ThEnd,
                $store->FrStart,
                $store->FrEnd,
                $store->SaStart,
                $store->SaEnd,
                $store->SuStart,
                $store->SuEnd,
                $store->HolidayStart,
                $store->HolidayEnd,
                $store->note,
                $store->accept_credit_card ? Store::$is_published[$store->accept_credit_card] : '不可能',
                $store->credit_card_type,
                $store->park_info,
                $store->description,
                $store->is_published ? Store::$is_published[$store->is_published] : '非公開',
            ));
        }

        fclose($handle);
    }


    protected function __array_combine($arr1, $arr2)
    {
        $count = min(count($arr1), count($arr2));
        return array_combine(array_slice($arr1, 0, $count), array_slice($arr2, 0, $count));
    }

    public function postImportCsv(Request $request)
    {
        setlocale(LC_ALL, 'ja_JP.UTF-8');

        $delimiter = ',';
        $file = $request->file('csv');
        if ($file) {
            $filename = $file->getRealPath();
            if (!file_exists($filename) || !is_readable($filename))
                return FALSE;

            $header = NULL;
            $data = array();

            if (($handle = fopen($filename, 'r+')) !== FALSE) {
                $handle = fopen($filename, 'r+');
                fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
                while (($row = fgetcsv($handle, 100000, $delimiter)) !== FALSE) {
                    if (!$header)
                        $header = $row;
                    else
                        $data[] = $this::__array_combine($header, $row);
                }
                fclose($handle);
            }
//            pr($data);die;
            $headerNumber = count($header);
            $working_time = array();
            $dataSave = array();
            DB::beginTransaction();
            $openHours = array_flip(Store::$hoursOpen);
            $companyId = $this::getCurrentCompany('id');
            $contractStore = $this->getCurrentCompany('contract_store');
            $numberStore = count(Store::where('company_id', '=', $companyId)->whereNull('is_deleted')->get());
            $availabeStoreNum = $contractStore - $numberStore;
            $storePublic = Store::where('company_id', $this->getCurrentCompany('id'))->whereNull('is_deleted')->where('is_published', 1)->count();
            $availabeStorePublic = $contractStore - $storePublic;


            $newRecordNum = 0;
            $updateRecordNum = 0;
            $countPublic = 0;
            foreach ($data as $d) {
                $arrTemp = array_values($d);

                // check csv format
                if (count($d) != $headerNumber) {
                    DB::rollBack();
                    return redirect('company/stores')->withErrors(__('CSV format is not correct'))
                        ->withInput();
                }

                $working_time['data'] = [
                    [
                        'id' => 0,
                        'title' => '月',
                        'start' => !empty($d[__('StartMonday')]) ? $d[__('StartMonday')] : '',
                        'end' => !empty($d[__('EndMonday')]) ? $d[__('EndMonday')] : ''
                    ],
                    [
                        'id' => 1,
                        'title' => '火',
                        'start' => !empty($d[__('StartTuesday')]) ? $d[__('StartTuesday')] : '',
                        'end' => !empty($d[__('EndTuesday')]) ? $d[__('EndTuesday')] : ''
                    ],
                    [
                        'id' => 2,
                        'title' => '水',
                        'start' => !empty($d[__('StartWednesday')]) ? $d[__('StartWednesday')] : '',
                        'end' => !empty($d[__('EndWednesday')]) ? $d[__('EndWednesday')] : ''
                    ],
                    [
                        'id' => 3,
                        'title' => '木',
                        'start' => !empty($d[__('StartThursday')]) ? $d[__('StartThursday')] : '',
                        'end' => !empty($d[__('EndThursday')]) ? $d[__('EndThursday')] : ''
                    ],
                    [
                        'id' => 4,
                        'title' => '金',
                        'start' => !empty($d[__('StartFriday')]) ? $d[__('StartFriday')] : '',
                        'end' => !empty($d[__('EndFriday')]) ? $d[__('EndFriday')] : ''
                    ],
                    [
                        'id' => 5,
                        'title' => '土',
                        'start' => !empty([__('StartSaturday')]) ? $d[__('StartSaturday')] : '',
                        'end' => !empty($d[__('EndSaturday')]) ? $d[__('EndSaturday')] : ''
                    ],
                    [
                        'id' => 6,
                        'title' => '日',
                        'start' => !empty($d[__('StartSunday')]) ? $d[__('StartSunday')] : '',
                        'end' => !empty($d[__('EndSunday')]) ? $d[__('EndSunday')] : ''
                    ],
                    [
                        'id' => 7,
                        'title' => '祝日',
                        'start' => !empty($d[__('StartHoliday')]) ? $d[__('StartHoliday')] : '',
                        'end' => !empty($d[__('EndHoliday')]) ? $d[__('EndHoliday')] : ''
                    ],

                ];

                $i = 0;
                $this->requiredWorkingTime = true;
                foreach ($working_time['data'] as $time) {
                    if ($time['start'] == $openHours['休'] || $time['end'] == $openHours['休'] || $time['start'] == '' || $time['end'] == '') {
                        $time['start'] = $openHours['休'];
                        $time['end'] = $openHours['休'];
                        $working_time['data'][$i] = $time;
                    } else {
                        $this->requiredWorkingTime = false;
                    }
                }

                $working_time['note'] = !empty($d[__('Comment')]) ? $d[__('Comment')] : '';
                $dataSave['company_id'] = $this::getCurrentCompany('id');
                $dataSave['name'] = !empty($d[__('StoreName')]) ? $d[__('StoreName')] : '';
                $dataSave['internal_code'] = !empty($d[__('StoreID')]) ? $d[__('StoreID')] : '';
                $dataSave['postal_code'] = !empty($d[__('PostalCode')]) ? trim($d[__('PostalCode')]) : '';
                $dataSave['province'] = !empty($d[__('Prefectures')]) ? $d[__('Prefectures')] : '';
                $dataSave['city1'] = !empty($d[__('City')]) ? $d[__('City')] : '';
                $dataSave['address'] = !empty($d[__('AddressFull')]) ? $d[__('AddressFull')] : '';
                $dataSave['phone_number'] = !empty($d[__('PhoneNumber')]) ? trim($d[__('PhoneNumber')]) : '';
                $dataSave['fax_number'] = !empty($d[__('FaxNumber')]) ? trim($d[__('FaxNumber')]) : '';
                $dataSave['working_time'] = json_encode($working_time);
                $dataSave['accept_credit_card'] = !empty($d[__('CreditCardIsUse')]) ? $d[__('CreditCardIsUse')] : '';
                $dataSave['credit_card_type'] = !empty($d[__('CreditCardType')]) ? $d[__('CreditCardType')] : '';
                $dataSave['park_info'] = !empty($d[__('Parking')]) ? $d[__('Parking')] : '';
                $dataSave['description'] = !empty($d[__('NewFromTheStore')]) ? $d[__('NewFromTheStore')] : '';
                $dataSave['is_published'] = !empty($d[__('PublicOrPrivate')]) ? $d[__('PublicOrPrivate')] : '';
                $dataSave['note'] = !empty($d[__('Comment')]) ? $d[__('Comment')] : '';
                $action = $arrTemp[0];

                // check action is correct or incorrect.
                $this->action = false;
                if (empty($action) || (!empty($action) && $action != __('IsNew') && $action != __('IsUpdated'))) {
                    $this->action = true;
                }

                $this->isCreditCardType = false;
                if (!empty($dataSave['accept_credit_card']) && $dataSave['accept_credit_card'] == __('CreditCardUse')) {
                    $dataSave['accept_credit_card'] = 1;

                    if (empty($dataSave['credit_card_type'])) {
                        $this->isCreditCardType = true;
                    }
                } else {
                    $dataSave['accept_credit_card'] = 0;
                    $dataSave['credit_card_type'] = '';
                }
                pr($countPublic);
                if (!empty($dataSave['is_published']) && $dataSave['is_published'] === __('PublicStore')) {
                    $dataSave['is_published'] = 1;
                    $countPublic++;
                    if ($countPublic > $availabeStorePublic) {
                        DB::rollBack();
                        return redirect('company/stores')->withErrors(__('Not enought contract Store.'))
                            ->withInput();
                    }
                } else {
                    $dataSave['is_published'] = 0;
                }

                $validator = Validator::make($dataSave, array(
                    'internal_code' => 'required',
                    'name' => 'required|max:30',
                    'postal_code' => "required|regex:'^([0-9]){1,3}[-]([0-9]){1,4}$'",
                    'province' => 'required',
                    'city1' => 'required',
                    'address' => 'required|max:40',
                    'accept_credit_card' => 'required',
                    'phone_number' => "required|regex:'^([0-9]){1,5}[-]([0-9]){1,4}[-]([0-9]){1,4}$'",
                    'fax_number' => "regex:'^([0-9]){1,5}[-]([0-9]){1,4}[-]([0-9]){1,4}$'",
                    'description' => 'max:200',
                    'note' => 'max:100'
                ));

                $validator->after(function ($validator) {
                    if ($this->action) {
                        $validator->errors()->add('NewUpdated', __('New or updated status is not correct.'));
                    }

                    if ($this->requiredWorkingTime) {
                        $validator->errors()->add('working_time', __('The working time is not empty.'));
                    }

                    if ($this->isCreditCardType) {
                        $validator->errors()->add('CreditCardType', __('Credit Card Type is not empty.'));
                    }
                });

                if ($validator->fails()) {
                    DB::rollBack();
                    return redirect('company/stores')->with('errors', $validator->errors())->withInput();
                }

                unset($dataSave['note']);

                if ($this->action == false) {
                    if (!empty($action) && $action == __('IsNew')) {
                        $newRecordNum = $newRecordNum + 1;

//                        if ($newRecordNum > $availabeStoreNum) {
//                            DB::rollBack();
//                            $validator->errors()->add('LimitStore', 'The store number is limit');
//                            return redirect('company/stores')->with('errors', $validator->errors())->withInput();
//
//                        }
                        $checkDuplicateCode = $this->checkStoreCodeDuplicate($dataSave['internal_code']);

                        if ($checkDuplicateCode) {
                            DB::rollBack();
                            return redirect('company/stores')->withErrors(['StoreIdDuplicated' => __('StoreID already exists')])
                                ->withInput(['internal_code' => $dataSave['internal_code']]);
                        }

                        Store::create($dataSave);
                    } else if (!empty($action) && $action == __('IsUpdated')) {
                        $updateRecordNum = $updateRecordNum + 1;
                        $store = Store::whereInternalCode($d[__('StoreID')])->first();

                        if ($store) {
                            Store::whereInternalCode($d[__('StoreID')])->update($dataSave);
                        } else {
                            DB::rollBack();
                            return redirect('company/stores')->withErrors(['StoreIdNotExist' => __('StoreID is not exists')])
                                ->withInput(['internal_code' => $dataSave['internal_code']]);
                        }
                    }
                }
            }
        }
        DB::commit();
        return redirect('company/stores')->with('status', 'Successfully');
    }

    /**
     * Shows current store profile
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getShow($alias)
    {

        $store = Store::whereAlias($alias)->first();

        if (empty($store)) {
            return view('errors.404');
        } else {
            return view('company.stores.show')->with('store', $store->toArray());
        }
    }

    public function getCreate()
    {
        $storeInputSetting = Store::getStoreInputSetting();
        $store = array();
        $provinces = Province::lists('name', 'name')->toArray();
        array_unshift($provinces, '&nbsp;');

        $contractStore = $this->getCurrentCompany('contract_store');
        $storePublic = Store::where('company_id', $this->getCurrentCompany('id'))->where('is_published', 1)->whereNull('is_deleted')->count();
        $enableCreate = $contractStore > $storePublic ? true : false;

        return view('company.stores.create', [
            'storeInputSetting' => $storeInputSetting,
            'store' => $store,
            'provinces' => $provinces,
            'enableCreate' => $enableCreate,
            'jsonData' => [
                'store' => $store,
                'cities' => array(),
                'provinces' => $provinces,
                'getCitiesListUrl' => action('Company\StoresController@getCitiesList')
            ]
        ]);
    }

    /**
     * Gets user detail
     * @param $alias
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEdit($alias)
    {
        $store = Store::whereAlias($alias)->whereNull('is_deleted')->first();
        if (empty($store)) {
            return redirect('company/stores');
        }
        $provinces = Province::lists('name', 'name')->toArray();

        $cities = City::select('cities_master.id', 'cities_master.name')->join('province_master', 'cities_master.province_id', '=', 'province_master.id')
            ->where('province_master.name', '=', $store->province)->get()->toArray();
        $contractStore = $this->getCurrentCompany('contract_store');
        $storePublic = Store::where('company_id', $this->getCurrentCompany('id'))->where('is_published', 1)->whereNull('is_deleted')->count();
        $enableCreate = $contractStore > $storePublic ? true : false;

        //Store Address
        $provinceName = $store->province;
        $issetProvince = Province::where('name', $provinceName)->first();
        $issetCity = City::where('cities_master.name', $store->city1)
            ->join('province_master', 'province_master.id', '=', 'cities_master.province_id')
            ->where(function ($query) use ($provinceName) {
                $query->where('province_master.name', '=', $provinceName);
            })
            ->first();
//        pr($issetCity->toArray());
//        die;
        $allowAdd['province'] = '';
        $allowAdd['city'] = '';
        if (empty($issetProvince)) {
            $allowAdd['province'] = $provinceName;
            if (empty($issetCity)) {
                $allowAdd['city'] = $store->city1;
            }
        }


        return view('company.stores.edit', [
            'store' => $store->toArray(),
            'provinces' => $provinces,
            'enableCreate' => $enableCreate,
            'jsonData' => [
                'store' => $store->toArray(),
                'cities' => $cities,
                'provinces' => $provinces,
                'getCitiesListUrl' => action('Company\StoresController@getCitiesList'),
                'postDeleteStoreUrl' => action('Company\StoresController@postDelete'),
                'getStoreIndexUrl' => action('Company\StoresController@getIndex'),
                'allowAdd' => $allowAdd
            ]
        ]);
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

    public function postDelete(Request $request)
    {
        $result = Store::where('id', $request['store_id'])->update(['is_deleted' => 1]);
        return r_ok($result);
    }

    /**
     * Updates current store
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function postUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), array(
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

        if (!empty($request['alias'])) {
            $checkDuplicateCode = $this->checkStoreCodeDuplicate($request['internal_code'], $request['alias'], true);

            if ($checkDuplicateCode) {
                return redirect('company/stores/edit/' . $request['alias'])->withErrors(['StoreIdDuplicated' => __('StoreID already exists')])
                    ->withInput(['internal_code' => $request['internal_code']]);
            }
        } else {
            $checkDuplicateCode = $this->checkStoreCodeDuplicate($request['internal_code']);

            if ($checkDuplicateCode) {
                return redirect('company/stores/create')->withErrors(['StoreIdDuplicated' => __('StoreID already exists')])
                    ->withInput(['internal_code' => $request['internal_code']]);
            }
        }

        if ($validator->fails()) {
            return redirect('company/stores/edit/' . $request['alias'])->with('errors', $validator->errors())->withInput();
        }

        $data['internal_code'] = $request['internal_code'];
        $data['name'] = $request['name'];
        $data['postal_code'] = implode('-', $request['postal_code']);
        $data['province'] = $request['province'];
        $data['city1'] = $request['city1'];

        $data['address'] = $request['address'];
        $data['phone_number'] = implode('-', $request['phone_number']);
        $data['fax_number'] = implode('-', $request['fax_number']);

        $data['accept_credit_card'] = $request['accept_credit_card'];
        $data['park_info'] = $request['park_info'];
        $data['description'] = $request['description'];
        $data['map_coordinates_lat'] = $request['map_coordinates_lat'];
        $data['map_coordinates_long'] = $request['map_coordinates_long'];
        $data['update_staff_id'] = $this->getCurrentStaff('id');
        $data['is_published'] = $request['is_published'];
        $data['credit_card_type'] = !empty($request['credit_card_type']) ? $request['credit_card_type'] : '';

        foreach (Store::$days as $key => $value) {
            $time_open = isset($request['times_open'][$key]) ? $request['times_open'][$key] : '休';
            $time_close = isset($request['times_close'][$key]) ? $request['times_close'][$key] : '休';
            $working_time['data'][] = array(
                'id' => $key,
                'title' => $value,
                'start' => $time_close == '休' ? '休' : $request['times_open'][$key],
                'end' => $time_open == '休' ? '休' : $request['times_close'][$key]
            );
        }

        $working_time['note'] = $request['note'];
        $data['working_time'] = json_encode($working_time);

        if (!file_exists(public_path('images/stores'))) {
            mkdir(public_path('images/stores'), 0777, true);
        }

        $image = $request->file('photo_url');

        if (!empty($image)) {
            $filename = 'StoreImage' . time() . '.' . $image->getClientOriginalExtension();
            $path = 'images/stores/' . $filename;
            $data['photo_url'] = $path;

            move_uploaded_file($image->getRealPath(), $path);
        }


        if (!empty($request['alias'])) {
            $store = Store::whereAlias($request['alias'])->first();
            if (!empty($image)) {
                @unlink(public_path($store->photo_url));//unlink old password
            }
            Store::whereAlias($request['alias'])->update($data);
            return redirect('company/stores/show/' . $store->alias)->with('status', 'Successfully');
        } else {
            $data['company_id'] = $this::getCurrentCompany('id');
            Store::create($data);
            return redirect('company/stores');
        }
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
                ->whereNull('is_deleted')
                ->count();
        } else {
            $store = Store::whereInternalCode($storeCode)
                ->whereCompanyId($companyId)
                ->whereNull('is_deleted')
                ->count();
        }

        if ($store > 0) {
            return true;
        }

        return false;

    }

}