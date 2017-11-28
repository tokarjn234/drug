<?php

namespace App\Models;

use Hamcrest\Core\Set;
use Illuminate\Database\Eloquent\Model;

class Setting extends AppModel
{
    protected $table = 'settings';

    public $fillable = [
        'id', 'name', 'key', 'value', 'created_at', 'updated_at', 'deleted_at', 'create_staff_id', 'update_staff_id', 'delete_staff_id', 'company_id'
    ];

    /**
     * Read config from settings table
     * @param $key
     * @return null|string
     */
    public static function read($key, $default = null)
    {
        $split = explode('.', $key);
        $name = array_shift($split);
        $key = implode($split, '.');

        $setting = Setting::where('name', '=', $name)
            ->where('key', '=', $key)
            ->where('company_id', '=', Company::current('id'))
            ->where('store_id', '=', Store::current('id'))
            ->first();

        return empty ($setting) ? $default : $setting->value;

    }

    /**
     * Read config from settings table
     * @param $key
     * @return null|string
     */
    public static function companyRead($key, $default = null)
    {
        $split = explode('.', $key);
        $name = array_shift($split);
        $key = implode($split, '.');

        $setting = Setting::where('name', '=', $name)
            ->where('key', '=', $key)
            ->where('company_id', '=', Company::current('id'))
            ->first();


        return empty ($setting) ? $default : $setting->value;

    }

    /**
     * Read config from settings table
     * @param $key
     * @return null|string
     */
    public static function mediaidRead($key, $default = null, $companyId = null)
    {
        $split = explode('.', $key);
        $name = array_shift($split);
        $key = implode($split, '.');
        if (!empty($companyId)) {
            $setting = Setting::where('name', '=', $name)
                ->where('key', '=', $key)
                ->where('company_id', '=', $companyId)
                ->first();
        } else {
            $setting = Setting::where('name', '=', $name)
                ->where('key', '=', $key)
                ->first();
        }

        return empty ($setting) ? $default : $setting->value;

    }

    /**
     * Read config from settings table
     * @param $key
     * @return null|string
     */
    public static function companyReadOnly($key, $default = null)
    {
        $split = explode('.', $key);
        $name = array_shift($split);
        $key = implode($split, '.');

        $setting = Setting::where('name', '=', $name)
            ->where('key', '=', $key)
            ->where('company_id', '=', Company::current('id'))
            ->whereNull('store_id')
            ->first();


        return empty ($setting) ? $default : $setting->value;

    }

    /**
     * Writes setting
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function write($key, $value)
    {
        $split = explode('.', $key);
        $name = array_shift($split);
        $key = implode($split, '.');
        $storeId = Store::current('id');
        $companyId = Company::current('id');
        $staffId = Staff::current('id');

        $setting = Setting::where('name', '=', $name)
            ->where('key', '=', $key)
            ->where('company_id', '=', $companyId)
            ->where('store_id', '=', $storeId)
            ->first();

        if (empty ($setting)) {
            $setting = new Setting();
            $setting->create_staff_id = $staffId;
        } else {
            $setting->update_staff_id = $staffId;
        }

        $setting->name = $name;
        $setting->company_id = $companyId;
        $setting->store_id = $storeId;
        $setting->key = $key;

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $setting->value = $value;

        return $setting->save();
    }

    /**
     * Writes setting
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function companyWrite($key, $value)
    {
        $split = explode('.', $key);
        $name = array_shift($split);
        $key = implode($split, '.');
        $companyId = Company::current('id');
        $staffId = Staff::current('id');

        $setting = Setting::where('name', '=', $name)
            ->where('key', '=', $key)
            ->where('company_id', '=', $companyId)
            ->where('store_id', '=', NULL)
            ->first();

        if (empty ($setting)) {
            $setting = new Setting();
            $setting->create_staff_id = $staffId;
        } else {
            $setting->update_staff_id = $staffId;
        }

        $setting->name = $name;
        $setting->company_id = $companyId;
        $setting->key = $key;

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $setting->value = $value;

        return $setting->save();
    }

    /**
     * Writes setting
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function mediaidWrite($key, $value, $companyId = null)
    {
        $split = explode('.', $key);
        $name = array_shift($split);
        $key = implode($split, '.');
        $staffId = Staff::current('id');

        $setting = Setting::where('name', '=', $name)
            ->where('key', '=', $key)
            ->where('company_id', '=', $companyId)
            ->where('store_id', '=', NULL)
            ->first();

        if (empty ($setting)) {
            $setting = new Setting();
            $setting->create_staff_id = $staffId;
        } else {
            $setting->update_staff_id = $staffId;
        }

        $setting->name = $name;
        $setting->company_id = $companyId;
        $setting->key = $key;

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $setting->value = $value;

        return $setting->save();
    }

    public static function getSettingsRegister($companyId, $name, $arrFields, $fieldName = '')
    {
        $registerSettings = Setting::whereCompanyId($companyId)->whereName($name)->whereIn('key', array_keys($arrFields))->lists('value', 'key');
        if (empty ($registerSettings)) {
            $registerSettings = [];
        }
        foreach ($arrFields as $field => $value) {
            if (isset($registerSettings[$field])) {
                $registerSettings[$field] = json_decode($registerSettings[$field], true);
            } else {
                $registerSettings[$field] = $value;
            }
        }
        if ($fieldName == '') {
            return $registerSettings;
        } else {
            return !isset($registerSettings[$fieldName]) ? null : $registerSettings[$fieldName];
        }
    }

    // Return number reset pass
    public static function getResetPassCount($company)
    {
        $number = 5;
//        $numberST = Setting::whereCompanyId($company)->whereName($name)->whereIn('key', array_keys($arrFields))->lists('value', 'key');
        return $number;
    }

}
