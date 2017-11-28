<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Store extends AppModel implements IDataRender
{
    use TDataRender;
    const STATUS_CREATE_AT = 0;
    const STATUS_RECEIVED = 1;
    const STATUS_PREPARED = 2;
    const STATUS_INVALID = 3;
    const VIEW_BY_DAYS = 0;
    const VIEW_BY_MONTH = 1;

    public static $days = ["月", "火", "水", "木", "金", "土","日","祝日"];

    public static $daysConvert = ["日", "月", "火", "水", "木", "金", "土"];

    public static $title = ["集計", "日別集計", "月別集計"];

    public static $is_published = ["非公開", "公開"];

    public static $is_impossible = ["不可", "可能"];

    public static $hoursOpen = [
        '休' => '休',
        '00:00' => '0:00',
        '00:15' => '0:15',
        '00:30' => '0:30',
        '00:45' => '0:45',
        '01:00' => '1:00',
        '01:15' => '1:15',
        '01:30' => '1:30',
        '01:45' => '1:45',
        '02:00' => '2:00',
        '02:15' => '2:15',
        '02:30' => '2:30',
        '02:45' => '2:45',
        '03:00' => '3:00',
        '03:15' => '3:15',
        '03:30' => '3:30',
        '03:45' => '3:45',
        '04:00' => '4:00',
        '04:15' => '4:15',
        '04:30' => '4:30',
        '04:45' => '4:45',
        '05:00' => '5:00',
        '05:15' => '5:15',
        '05:30' => '5:30',
        '05:45' => '5:45',
        '06:00' => '6:00',
        '06:15' => '6:15',
        '06:30' => '6:30',
        '06:45' => '6:45',
        '07:00' => '7:00',
        '07:15' => '7:15',
        '07:30' => '7:30',
        '07:45' => '7:45',
        '08:00' => '8:00',
        '08:15' => '8:15',
        '08:30' => '8:30',
        '08:45' => '8:45',
        '09:00' => '9:00',
        '09:15' => '9:15',
        '09:30' => '9:30',
        '09:45' => '9:45',
        '10:00' => '10:00',
        '10:15' => '10:15',
        '10:30' => '10:30',
        '10:45' => '10:45',
        '11:00' => '11:00',
        '11:15' => '11:15',
        '11:30' => '11:30',
        '11:45' => '11:45',
        '12:00' => '12:00'
    ];

    public static $hoursClose = [
        '休' => '休',
        '12:00' => '12:00',
        '12:15' => '12:15',
        '12:30' => '12:30',
        '12:45' => '12:45',
        '13:00' => '13:00',
        '13:15' => '13:15',
        '13:30' => '13:30',
        '13:45' => '13:45',
        '14:00' => '14:00',
        '14:15' => '14:15',
        '14:30' => '14:30',
        '14:45' => '14:45',
        '15:00' => '15:00',
        '15:15' => '15:15',
        '15:30' => '15:30',
        '15:45' => '15:45',
        '16:00' => '16:00',
        '16:15' => '16:15',
        '16:30' => '16:30',
        '16:45' => '16:45',
        '17:00' => '17:00',
        '17:15' => '17:15',
        '17:30' => '17:30',
        '17:45' => '17:45',
        '18:00' => '18:00',
        '18:15' => '18:15',
        '18:30' => '18:30',
        '18:45' => '18:45',
        '19:00' => '19:00',
        '19:15' => '19:15',
        '19:30' => '19:30',
        '19:45' => '19:45',
        '20:00' => '20:00',
        '20:15' => '20:15',
        '20:30' => '20:30',
        '20:45' => '20:45',
        '21:00' => '21:00',
        '21:15' => '21:15',
        '21:30' => '21:30',
        '21:45' => '21:45',
        '22:00' => '22:00',
        '22:15' => '22:15',
        '22:30' => '22:30',
        '22:45' => '22:45',
        '23:00' => '23:00',
        '23:15' => '23:15',
        '23:30' => '23:30',
        '23:45' => '23:45',
        '24:00' => '24:00',
    ];

    public $fillable = [
        'district_id', 'is_published', 'company_id', 'internal_code', 'name', 'photo_url', 'postal_code',
        'address', 'province', 'city1', 'phone_number', 'fax_number', 'accept_credit_card', 'park_info', 'overtime_alert',
        'allow_reply', 'map_coordinates_lat', 'map_coordinates_long', 'editable', 'status', 'created_staff_id', 'created_at', 'update_staff_id',
        'updated_at', 'delete_staff_id', 'deleted_at', 'is_published', 'credit_card_type', 'working_time', 'description'
    ];

    public static $defaultInputSetting  =  [
        'name' => ['display' => true, 'edit' => true],
        'photo_url' => ['display' => true, 'edit' => false],
        'postal_code' => ['display' => true, 'edit' => false],
        'address' => ['display' => true, 'edit' => false],
        'phone_number' => ['display' => true, 'edit' => false],
        'fax_number' => ['display' => true, 'edit' => false],
        'working_time' => ['display' => true, 'edit' => false],
        'accept_credit_card' => ['display' => true, 'edit' => false, 'data' => ['accept' => true, 'card_type' => '']],
        'park_info' => ['display' => true, 'edit' => false, 'data' => ''],
        'description' => ['display' => true, 'edit' => false, 'data' => ''],
        'note_working_time' => ['display' => true, 'edit' => false, 'data' => ''],
        'internal_code' => ['display' => true, 'edit' => false, 'data' => ''],
    ];

    /**
     * Gets store input settings
     * @params $returnArray
     * @return object
     */
    public static function getStoreInputSetting($returnArray = false) {
        $setting = self::$defaultInputSetting;

        $storeInputSetting = Setting::companyRead('CompanyStoreSetting.store_info_input');

        $storeInputSetting = json_decode($storeInputSetting, true);

        if (!empty ($storeInputSetting)) {

            // Mergers options
            foreach ($setting as $field => &$options) {
                if (isset ($storeInputSetting[$field])) {
                    if (isset ($storeInputSetting[$field]['display'])) {
                        $options['display'] = $storeInputSetting[$field]['display'];
                    }

                    if (isset ($storeInputSetting[$field]['edit'])) {
                        $options['edit'] = $storeInputSetting[$field]['edit'];
                    }

                    if (isset ($storeInputSetting[$field]['data'])) {
                        $options['data'] = $storeInputSetting[$field]['data'];
                    }
                }
            }


        }

        return $returnArray ? $setting : (object) $setting;
    }


//    public function district()
//    {
//        return $this->belongsTo('App\Models\District');
//    }

//    public function city()
//    {
//        return $this->belongsTo('App\Models\City');
//    }
//
//    public function province()
//    {
//        return $this->belongsTo('App\Models\Province');
//    }
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    /**
     * Gets current store
     * @param null $field
     * @return array
     */
    public static function current($field = null) {
        static $store = null;

        if (!$store) {
            $store = Store::find(session('CurrentStore')->id);
        }

        if ($field) {
            return $store->{$field};
        }

        return $store;
    }
}