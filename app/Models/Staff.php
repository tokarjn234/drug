<?php


namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Auth;

class Staff extends AppModel implements AuthenticatableContract, AuthorizableContract, IDataRender
{
    use Authenticatable, Authorizable, TDataRender;
    protected $table = 'staffs';

    protected $fillable = [
        'username', 'email', 'password', 'first_name', 'last_name', 'job_category',
        'first_name_kana', 'last_name_kana', 'gender', 'birthday', 'authority', 'department',
        'zip_code', 'province', 'city1', 'city2', 'address', 'phone_number', 'company_id', 'status', 'account_type', 'number_login_retry', 'last_status'
    ];

    const STATUS_LOCKOUT = 1;
    const STATUS_ACCOUNT_LOCK = 2;
    const STATUS_UNREGISTER = 3;
    const STATUS_DELETED = 4;
    const STATUS_REGISTER = 5;


    const GENDER_FEMALE = 0;
    const GENDER_MALE = 1;
    const GENDER_OTHERS = 2;

    const ACCOUNT_TYPE_STORE = 0;
    const ACCOUNT_TYPE_COMPANY = 1;
    const ACCOUNT_TYPE_MEDIAID = 2;

    const STORE_ACCESS_NAME = '受信通知API';
    const STORE_ACCESS_API_NAME = '店舗管理アプリ';
    const COMPANY_ACCESS_NAME = '本部管理アプリ';
    const MEDIAID_ACCESS_NAME = 'システム管理アプリ';

    public static $statuses = [
        self::STATUS_REGISTER => '登録',
        self::STATUS_LOCKOUT => 'ロックアウト',
        self::STATUS_ACCOUNT_LOCK => 'アカウントロック',
        self::STATUS_UNREGISTER => '未登録',
        self::STATUS_DELETED => '削除済',

    ];

    const JOB_CATEGORY_PHARMACIST = 0;
    const JOB_CATEGORY_CLERICAL_STAFF = 1;
    const JOB_CATEGORY_REGISTRATION_SELLER = 2;
    const JOB_CATEGORY_OTHER = 3;

    public static $jobCategory = [
        self::JOB_CATEGORY_PHARMACIST => '薬剤師',
        self::JOB_CATEGORY_CLERICAL_STAFF => '事務スタッフ',
        self::JOB_CATEGORY_REGISTRATION_SELLER => '登録販売者',
        self::JOB_CATEGORY_OTHER => 'その他'
    ];

    /**
     * Gets current logged in staff
     * @param null $field
     * @return mixed
     */
    public static function current($field = null)
    {
        static $staff = null;

        if ($staff === null) {
            $staff = Auth::user();

            if (empty ($staff)) {
                throw new \Exception('Current loggin staff record was not found');
            }

        }

        if ($field) {
            return $staff->{$field};
        }

        return $staff;
    }

    /**
     * IDataRender::getRenderSettings implements
     * @return array
     */
    public static function getRenderSettings()
    {

        return [
            'created_at' => function ($createdAt) {
                $t = strtotime($createdAt);
                return date('m/d', $t) . '<br>' . date('h:i', $t);
            },
            '$gender' => function ($item) {
                return $item['gender'] ? '男性' : '女性';
            },
            'birthday' => function ($birthday) {
                $t = strtotime($birthday);
                return date('y/m/d', $t);
            },
            '$age' => function ($item) {
                if ($item['birthday'] == '0000-00-00' || $item['birthday'] == '') return '';
                $age = date_diff(date_create($item['birthday']), date_create('now'))->y;
                return $age;
            },
            '$name_kana' => function ($item) {
                if ($item['first_name_kana'] == '' && $item['last_name_kana'] == '') return '-';
                $name_kana = $item['first_name_kana'] . $item['last_name_kana'];
                return $name_kana;
            },
            '$name' => function ($item) {
                if ($item['first_name'] == '' && $item['last_name'] == '') return '-';
                $name_kana = $item['first_name'] . $item['last_name'];
                return $name_kana;
            },
            'created_at_staff' => function ($item) {
                $t = strtotime($item['created_at']);
                return date('Y/m/d', $t) . '<br>' . date('h:i', $t);
            },
            'created_at_staff_24h' => function ($item) {
                $t = strtotime($item['last_access_at']);
                return empty($item['last_access_at']) ? '-' : date('Y/m/d', $t) . '<br>' . date('H:i', $t);
            },
            'status_string' => function ($item) {
                return empty($item['status']) ? '-' : self::$statuses[$item['status']];
            }
        ];
    }

    /**
     * Gets login settings
     */
    public static function getLoginSetting()
    {
        $setting = json_decode(Setting::companyRead('CompanyStoreSetting.setting_staff_login', '{}'));

        if (empty ($setting->password_expire) && empty ($setting->multi_account_login)) {
            $setting = (object)['password_expire' => '90days', 'multi_account_login' => true];
        }

        return (object)$setting;

    }

    public static function getLoginSettingCompany()
    {
        $setting = json_decode(Setting::companyRead('CompanySettingChangePass.setting_staff_login', '{}'));

        if (empty ($setting->password_expire) && empty ($setting->multi_account_login)) {
            $setting = (object)['password_expire' => '90days', 'multi_account_login' => true];
        }

        return (object)$setting;

    }

    public static function getLoginSettingMediaid()
    {
        $setting = json_decode(Setting::mediaidRead('MediaidSettingChangePass.setting_staff_login', '{}'));

        if (empty ($setting->password_expire) && empty ($setting->multi_account_login)) {
            $setting = (object)['password_expire' => '60days', 'multi_account_login' => true];
        }

        return (object)$setting;

    }

}