<?php


namespace App\Models;


class Company extends AppModel
{
    use TDataRender;

    const STATUS_PREPARE = 0;
    const STATUS_IN_USE = 1;
    const STATUS_CANCELLATION_COMPLETED = 2;

    public static $status = [
        self::STATUS_PREPARE => '準備中',
        self::STATUS_IN_USE => '利用中',
        self::STATUS_CANCELLATION_COMPLETED => '解約済',
    ];

    protected $fillable = [
        'name', 'name_manager', 'phone_number', 'fax', 'postal_code_headquarters', 'status', 'cert_add',
        'headquarters', 'bill_to_postal_code', 'bill_to_address', 'bill_to_destination', 'contract_store', 'staff_add', 'billable'
    ];

    public function meta_company()
    {
        return $this->hasOne('App\Models\MetaCompany');
    }

    public function store()
    {
        return $this->hasMany('App\Models\Store');
    }

    /**
     * Gets current company
     * @param null $field
     * @return array
     */
    public static function current($field = null)
    {
        static $company = null;

        if (!$company) {
            $company = Company::find(session('CurrentCompany')->id);
        }

        if ($field) {
            return $company->{$field};
        }

        return $company;
    }

    public static function getListStores()
    {
        return Store::whereCompanyId(session('CurrentCompany')->id)->lists('id')->toArray();
    }

    /**
     * IDataRender::getRenderSettings implements
     * @return array
     */
    public static function getRenderSettings()
    {

        return [
            'updated_at' => function ($item) {
                $t = strtotime($item);
                return date('Y/m/d', $t);
            },

            'status_string' => function ($item) {
                switch ($item['status']) {
                    case self::STATUS_PREPARE :
                        return self::$status[$item['status']];
                        break;
                    case self::STATUS_IN_USE :
                        return self::$status[$item['status']];
                        break;
                    case self::STATUS_CANCELLATION_COMPLETED :
                        return self::$status[$item['status']];
                        break;
                    default :
                        return '-';
                }
            },
            'id_cpn' => function ($item) {
                return str_pad($item['id'], 4, '0', STR_PAD_LEFT);
            },
            'all_staff' => function ($item) {
                $basicStaff = Setting::mediaidRead('MediaidSettingCompany.basicStaffPerStore', 0, $item['id']);
                $staffAdd = json_decode($item['staff_add'], true);
                return $basicStaff*$item['contract_store'] + $staffAdd['number'];
            }
        ];
    }
}