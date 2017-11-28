<?php


namespace App\Http\Controllers\Api;

use App\Models\City;
//use App\Models\District;
use App\Models\Province;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\JsonResponse;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;

class StoresController extends ApiAppController
{

    /**
     * @param $latitude1
     * @param $longitude1
     * @param $latitude2
     * @param $longitude2
     * @return array
     */
    public function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
    }

    /**
     * Search Store
     * @uri /api/stores/search-store/
     * @method GET
     * @param Request $request
     * @return array
     */

    public function getSearchStore(Request $request)
    {
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');

//        Search map_lat, map_long
        $map_lat = $request['map_lat'];
        $map_long = $request['map_long'];

        if (!empty($map_lat) && !empty($map_long)) {
            $stores = Store::where('company_id', $companyId)->whereNull('is_deleted')->whereNotNull('map_coordinates_lat')->whereNotNull('map_coordinates_long')->where('is_published', 1)->get()->toArray();

            if (!empty($stores)) {
                $newStores = [];
                foreach ($stores as $key => $value) {
                    $distance = $this->getDistanceBetweenPointsNew($map_lat, $map_long, $value['map_coordinates_lat'], $value['map_coordinates_long']);
                    if ($distance['kilometers'] <= 20) {
                        $settings = Setting::where('store_id', $value['id'])->whereIn('key', ['acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting'])->lists('value', 'key')->toArray();
                        $newStores[$key] = $value;
                        $newStores[$key]['number_location'] = $distance['kilometers'];
                        $newStores[$key]['settings'] = empty($settings) ? null : $settings;
                    }
                }
                uasort($newStores, function ($arr1, $arr2) {
                    $id1 = $arr1['number_location'];
                    $id2 = (int)$arr2['number_location'];
                    if ($id1 == $id2) return 0;
                    return ($id2 > $id1) ? -1 : 1;
                });
                $arr = [];
                $i = 0;
                foreach ($newStores as $k => $v) {
                    $arr[$i] = $v;
                    $i++;
                }
                return r_ok($arr, 'Successfully');
            } else {
                return r_ok(null, __('No data found'));
            }

        }
//        Search name province, city
        $province = $request['province'];
        $city = $request['city'];

        if (!empty($request['province']) && !empty($request['city'])) {
            $data = Store::where('company_id', $companyId)
                ->search('stores.city1', $city, ' ')
                ->search('stores.province', $province, ' ')
                ->whereNull('is_deleted')
                ->where('is_published', 1)
//                ->paginate($limit)
                ->get()
                ->toArray();
        }

        if (!empty($request['province']) && empty($request['city'])) {
            $data = Store::where('company_id', $companyId)
                ->search('stores.province', $province, ' ')
                ->whereNull('is_deleted')
                ->where('is_published', 1)
                ->get()
                ->toArray();
        }

        if (empty($data)) {
            $data = Store::where('company_id', $companyId)
                ->whereNull('is_deleted')
                ->where('is_published', 1)
                ->get()
                ->toArray();
        }
        foreach ($data as $key => $value) {
            $settings = Setting::where('store_id', $value['id'])->whereIn('key', ['acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting'])->lists('value', 'key')->toArray();
            if (empty($settings)) {
                $settings['acceptOrderOnNonBusinessHour'] = 1;
                $settings['showAlertAtNight'] = 1;
                $settings['patientReplySetting'] = 1;
            }
            $data[$key]['settings'] = $settings;
        }
        return empty($data) ? r_ok(null, __('No data found')) : r_ok($data);

    }

    /**
     * Search name Store
     * @uri /api/stores/search-name-store/
     * @method GET
     * @param Request $request
     * @return array
     */
    public function getSearchNameStore(Request $request)
    {
//        $nameStore = $request['name_store'];
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');
        $data = Store::where('company_id', $companyId)
            ->whereNull('is_deleted')
            ->where('is_published', 1)
//            ->search('name', $nameStore, ' ')
            ->orderBy('province', 'asc')
            ->limit(20)
            ->get();
        if (empty($data)) {
            return r_ok([], '');
        }
        $data = $data->toArray();
        usort($data, function ($arr1, $arr2) {
            if ($arr1["province"] . $arr1["city1"] > $arr2["province"] . $arr2["city1"]) {
                return 1;
            } else if ($arr1["province"] . $arr1["city1"] == $arr2["province"] . $arr2["city1"]) {
                return 0;
            } else {
                return -1;
            }
        });

        foreach ($data as $key => $value) {
            $settings = Setting::where('store_id', $value['id'])->whereIn('key', ['acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting'])->lists('value', 'key')->toArray();
            if (empty($settings)) {
                $settings['acceptOrderOnNonBusinessHour'] = 1;
                $settings['showAlertAtNight'] = 1;
                $settings['patientReplySetting'] = 1;
            }
            $data[$key]['settings'] = $settings;
        }

        return r_ok($data, 'Successfully');
    }


    /**
     * Detail Store
     * @uri /api/stores/detail-store/
     * @method GET
     * @param Request $request
     * @return array
     */

    public function getDetailStore(Request $request)
    {
        $idStore = $request['id'];
        $store = Store::where('id', $idStore)->first();
        if (empty($store)) {
            return r_err([], __('Data not found'));
        }
        $settings = Setting::where('store_id', $idStore)->whereIn('key', ['acceptOrderOnNonBusinessHour', 'showAlertAtNight', 'patientReplySetting'])->lists('value', 'key')->toArray();
        if (empty($settings)) {
            $settings['acceptOrderOnNonBusinessHour'] = 1;
            $settings['showAlertAtNight'] = 1;
            $settings['patientReplySetting'] = 1;
        }
        $store->settings = $settings;

        return r_ok($store->toArray());
    }

    /**
     * List Provinces
     * @uri /api/stores/list-province/
     * @method GET
     * @param
     * @return array
     */

    public function getListProvince(Request $request)
    {
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');
//        var_dump($companyId);die;
        $provinces = Store::distinct()->where('company_id', $companyId)->whereNull('is_deleted')->where('is_published', 1)->lists('province');
//        var_dump($provinces->toArray());die;
        return empty($provinces) ? r_ok([], 'Successfully') : r_ok($provinces->toArray(), 'Successfully');
    }

    /**
     * List City
     * @uri /api/stores/list-city/
     * @method GET
     * @param
     * @return array
     */

    public function getListCity(Request $request)
    {
        $province = str_replace(' ', '%', $request['province']);
        $companyId = Company::findByAlias($request->headers->get('company'), 'id');
        $city = Store::distinct()
            ->where('province', 'like', '%' . $province . '%')
            ->whereNull('is_deleted')->where('is_published', 1)
            ->where('company_id', $companyId)->lists('city1');
        return empty($city) ? r_ok([], 'Successfully') : r_ok($city->toArray(), 'Successfully');
    }
}