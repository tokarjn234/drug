<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Crypt;
use App\Models\DebugLog;
use Mockery\CountValidator\Exception;

class User extends AppModel implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    IDataRender
{
    use Authenticatable, Authorizable, CanResetPassword, TDataRender;


    const STATUS_NON_MEMBERS = 0;
    const STATUS_TEMPORARY_MEMBERS = 1;
    const STATUS_TEMPORARY_MEMBERS_CANCEL = 2;
    const STATUS_MEMBERS = 3;
    const STATUS_EXITED = 4;
    const STATUS_EXPELLED = 5;
    const STATUS_BLACKLIST = 6;

    const GENDER_FEMALE = 0;
    const GENDER_MALE = 1;
    const GENDER_OTHERS = 2;

    //Add account type
    const ACCOUNT_TYPE_USER = 5;
    const USER_ACCESS_NAME = '患者アプリAPI';

    public static $statuses = [
        self::STATUS_NON_MEMBERS => '仮登録',
        self::STATUS_TEMPORARY_MEMBERS => '仮登録',
        self::STATUS_TEMPORARY_MEMBERS_CANCEL => '仮登録キャンセル',
        self::STATUS_MEMBERS => '登録完了',
        self::STATUS_EXITED => '退会',
        self::STATUS_EXPELLED => '強制退会　(客様確認必要）',
        self::STATUS_BLACKLIST => 'ブラック(客様確認必要）',
    ];

    public static $genders = [
        '' => 'すべて',
        self::GENDER_FEMALE => '女性',
        self::GENDER_MALE => '男性',
        self::GENDER_OTHERS => '未設定',
    ];

    public static $months = [
        '' => 'すべて',
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10',
        11 => '11',
        12 => '12',
    ];

    public static $is_checkout = [
        true => 'あり',
        false => 'なし'
    ];

    public static $defaultRegisterSetting = [
        'first_name' => ['display' => true, 'required' => true],
        'first_name_kana' => ['display' => true, 'required' => true],
        'gender' => ['display' => false, 'required' => false],
        'birthday' => ['display' => true, 'required' => false],
        'phone_number' => ['display' => true, 'required' => true],
        'email' => ['display' => true, 'required' => true],
        'postal_code' => ['display' => true, 'required' => true],
        'address' => ['display' => true, 'required' => true],
        'drugbook_use' => ['display' => true, 'required' => false],
        'drugbrand_change' => ['display' => true, 'required' => false],
        'accept_saleinfo' => ['display' => true, 'required' => false],
        'accept_saleinfo_dm' => ['display' => true, 'required' => false],
    ];


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'first_name', 'last_name', 'exited_at', 'email_used',
        'first_name_kana', 'last_name_kana', 'gender', 'birthday',
        'province', 'city1', 'address', 'phone_number', 'notification_enable', 'postal_code', 'mail_reminder_time',
        'company_id', 'company_name', 'register_token', 'register_token_expire', 'status', 'accept_saleinfo_dm', 'accept_saleinfo', 'change_email_token', 'reset_pass_token'
    ];

    protected static $encryptFields = ['email', 'first_name', 'last_name', 'first_name_kana', 'last_name_kana', 'phone_number', 'address'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function getRememberTokenName()
    {
        return 'remember_token';
    }


    /**
     * IDataRender::getRenderSettings implements
     * @return array
     */
    public static function getRenderSettings()
    {
        return [
            'detail_created_at' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '-';
                if (empty($item['created_at'])) return '';
                $t = strtotime($item['created_at']);
                return date('Y/m/d', $t) . ' ' . date('h:i', $t);
            },
            'detail_order_created_at' => function ($item) {
                if (empty($item['order_created_at'])) return '';

                $t = strtotime($item['order_created_at']);
                return date('Y/m/d', $t) . ' ' . date('h:i', $t);
            },
            'created_at_check' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '-';

                if ($item['created_at'] == '') return '';

                $t = strtotime($item['created_at']);
                return date('Y/m/d', $t) . '<br>' . date('h:i', $t);
            },
            'order_created_at' => function ($orderCreatedAt) {
                if (empty($orderCreatedAt)) return '';

                $t = strtotime($orderCreatedAt);
                return date('Y/m/d', $t) . '<br>' . date('h:i', $t);
            },
            'gender_check' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '-';

                return @self::$genders[$item['gender']];
            },
            'gender_csv' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '';

                return @self::$genders[$item['gender']];
            },
            'birthday_check' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '-';
                if ($item['birthday'] == '') return '';
                $t = strtotime($item['birthday']);
                return date('Y/m/d', $t);
            },
            'birthday_csv' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '';
                if ($item['birthday'] == '') return '';
                $t = strtotime($item['birthday']);
                return date('Y/m/d', $t);
            },
            'age' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '';
                if ($item['birthday'] == '0000-00-00' || $item['birthday'] == '') return '';
                $age = date_diff(date_create($item['birthday']), date_create('now'))->y;
                return empty($age) ? '' : $age . ' 歳';
            },
            'age_check' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '-';
                if ($item['birthday'] == '0000-00-00' || $item['birthday'] == '') return '';
                $age = date_diff(date_create($item['birthday']), date_create('now'))->y;
                return empty($age) ? '' : $age . ' 歳';
            },
            'age_check_detail' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return '-';
                if ($item['birthday'] == '0000-00-00' || $item['birthday'] == '') return '';
                $age = date_diff(date_create($item['birthday']), date_create('now'))->y;
                return empty($age) ? '' : $age . '歳';
            },
            'is_checkout' => function ($item) {
                if ($item['status'] == self::STATUS_EXITED || $item['status'] == self::STATUS_EXPELLED)
                    return true;
                else
                    return false;
            },
            'short_order_code' => function ($item) {
                return substr(strstr($item['order_code'], "-"), 1);
            },
            'name' => function ($item) {
                $space = '';
                if (empty($item['first_name']) && empty($item['last_name'])) {
                    return '-';
                }
                if ($item['first_name'] && $item['last_name']) {
                    $space = '&nbsp;&nbsp;';
                }
                return $item['first_name'] . $space . $item['last_name'];
            },
            'name_kana' => function ($item) {
                $space = '';
                if (empty($item['first_name_kana']) && empty($item['last_name_kana'])) {
                    return '-';
                }
                if ($item['first_name_kana'] && $item['last_name_kana']) {
                    $space = '&nbsp;&nbsp;';
                }
                return $item['first_name_kana'] . $space . $item['last_name_kana'];
            },
            'postal_code_check' => function ($item) {
                if (empty($item['first_name_kana']) && empty($item['last_name_kana'])) {
                    return '-';
                }
                return $item['postal_code'];
            },
            'address_full' => function ($item) {
                if (empty($item['first_name_kana']) && empty($item['last_name_kana'])) {
                    return '-';
                }
                return $item['province'] . ' ' . $item['city1'] . ' ' . $item['address'];
            },
            'exited_time' => function ($item) {
                if (empty($item['exited_at'])) {
                    return '';
                }
                return date('Y/m/d H:i', strtotime($item['exited_at']));
            },
        ];
    }

    /**
     * Encrypts user data
     * @param $user
     * @return array
     */
    public static function encrypt($user)
    {
        try {
            if (is_object($user)) {
                foreach (self::$encryptFields as $field) {
                    if (!empty ($user->{$field})) {
                        $user->{$field} = Crypt::encrypt($user->{$field});
                    }

                }
            } else if (is_array($user)) {
                foreach (self::$encryptFields as $field) {
                    if (!empty ($user[$field])) {
                        $user[$field] = Crypt::encrypt($user[$field]);
                    }
                }
            }
        } catch (Exception $e) {
            DebugLog::error($e);
        }


        return $user;
    }

    /**
     * Decrypts user data
     * @param $user
     * @return array
     */
    public static function decrypt($user)
    {
        try {
            if (is_object($user)) {
                foreach (self::$encryptFields as $field) {
                    if (!empty ($user->{$field})) {
                        $user->{$field} = Crypt::decrypt($user->{$field});
                    }

                }
            } else if (is_array($user)) {
                foreach (self::$encryptFields as $field) {
                    if (!empty ($user[$field])) {
                        $user[$field] = Crypt::decrypt($user[$field]);
                    }
                }
            }
        } catch (Exception $e) {
            DebugLog::error($e);
        }

        return $user;
    }

    /**
     * @param $users
     */
    public static function encryptList($users)
    {
        foreach ($users as &$user) {
            $user = self::encrypt($user);
        }

        return $users;
    }

    /**
     * @param $users
     */
    public static function decryptList($users)
    {
        foreach ($users as &$user) {
            $user = self::decrypt($user);
        }

        return $users;
    }


    /**
     * @param  string $value
     * @return string
     */
    public function getEmailAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function getFirstNameAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function getLastNameAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function getFirstNameKanaAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function getLastNameKanaAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public static function getPhoneNumberAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function getAddressAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = encrypt_data($value);
        $this->attributes['email_index'] = md5($value);

    }

    /**
     * @param  string $value
     * @return string
     */
    public function setFirstNameAttribute($value)
    {
        $this->firstName = $value;
        $this->attributes['first_name'] = encrypt_data($value);

        if (!empty ($this->firstName) && !empty ($this->lastName)) {
            $this->attributes['full_name_index'] = get_reverted_index($this->firstName . ' ' . $this->lastName);
        }

    }

    /**
     * @param  string $value
     * @return string
     */
    public function setLastNameAttribute($value)
    {
        $this->lastName = $value;
        $this->attributes['last_name'] = encrypt_data($value);

        if (!empty ($this->firstName) && !empty ($this->lastName)) {
            $this->attributes['full_name_index'] = get_reverted_index($this->firstName . ' ' . $this->lastName);
        }

    }

    /**
     * @param  string $value
     * @return string
     */
    public function setFirstNameKanaAttribute($value)
    {
        $this->firstNameKana = $value;

        $this->attributes['first_name_kana'] = encrypt_data($value);

        if (!empty ($this->firstNameKana) && !empty ($this->lastNameKana)) {
            $this->attributes['full_name_kana_index'] = get_reverted_index($this->firstNameKana . ' ' . $this->lastNameKana);
        }
    }

    /**
     * @param  string $value
     * @return string
     */
    public function setLastNameKanaAttribute($value)
    {
        $this->lastNameKana = $value;

        $this->attributes['last_name_kana'] = encrypt_data($value);

        if (!empty ($this->firstNameKana) && !empty ($this->lastNameKana)) {
            $this->attributes['full_name_kana_index'] = get_reverted_index($this->firstNameKana . ' ' . $this->lastNameKana);
        }
    }

    /**
     * @param  string $value
     * @return string
     */
    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = encrypt_data($value);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = encrypt_data($value);
    }

}
