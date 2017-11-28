<?php


namespace App\Models;
use Crypt;

class Order extends AppModel implements IDataRender
{
    use TDataRender;
    const STATUS_RECEIVED = 0;
    const STATUS_RECEIVED_NOTIFIED = 1;
    const STATUS_PREPARED_NOTIFIED = 2;
    const STATUS_INVALID = 3;
    const STATUS_ALL = -1;

    const COMPLETE_FLAG_PENDING = 0;
    const COMPLETE_FLAG_SUCCESS = 1;
    const COMPLETE_FLAG_DELETE = 2;


    public static $statuses = [
        self::STATUS_ALL => 'すべて',
        self::STATUS_RECEIVED => '受付',
        self::STATUS_RECEIVED_NOTIFIED => '受付通知',
        self::STATUS_PREPARED_NOTIFIED => '完了',
        self::STATUS_INVALID => '無効'
    ];

    public static $drugBrandChanges = [
        '希望しない',
        '希望する'
    ];

    public static $drugBrandUses = [
        '希望しない',
        '希望する'
    ];

    public $fillable = [
        'id',
        'alias',
        'company_id',
        'order_code',
        'user_id',
        'store_id',
        'visit_at',
        'visit_at_string',
        'comment',
        'sent_received_msg_at',
        'sent_prepared_msg_at',
        'sent_other_msg_at',
        'status',
        'completed_flag',
        'created_at',
        'updated_at',
        'update_staff_id',
        'deleted_at',
        'delete_reason',
        'delete_staff_id',
        'sent_dispensed_msg_at'
    ];

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id');
    }

    public function photo()
    {
        return $this->hasMany('App\Models\Photo');
    }

    public function message()
    {
        return $this->hasMany('App\Models\Message');
    }

    /**
     * Parses order code
     * @example
     * '0001-160801-00001' => '160801-00001'
     * @param $code
     * @return string
     */
    public static function parseOrderCode($code, $delimiter = ' ')
    {
        list(, $id1, $id2) = @explode('-', $code);
        return $id1 . $delimiter . $id2;
    }

    /**
     * @param $visitAt
     */
    public static function parseVisitTimeString($visitAtString, $visitAt)
    {
        if ($visitAtString == '今日') {
            return '当日';
        } else if (strpos($visitAtString, '60分') !== false) {
            return sprintf('当日 %d時頃', date('H', strtotime($visitAt)));
        }

        return str_replace('今日', '当日', $visitAtString);
    }


    /**
     * IDataRender::getRenderSettings implements
     * @return array
     */
    public static function getRenderSettings()
    {

        return array(
            'order_code' => function ($orderCode) {
                return self::parseOrderCode($orderCode, '<br>');

            },
            
            'created_at' => function ($createdAt) {
                if (!$createdAt) {
                    return '';
                }

                $t = strtotime($createdAt);
                return date('m/d', $t) . '<br>' . date('H:i', $t);
            },

            '$status' => function ($item) {
                return @self::$statuses[$item['status']];
            }
            ,
            'visit_at_string' => function ($visitAtString, $item) {
                return self::parseVisitTimeString($visitAtString, $item['visit_at']);
            },
            'first_name' => function($value, $item) {
                if (empty ($value)) {
                    $value = empty ($item['first_name_kana']) ? '' : $item['first_name_kana'];
                }

                return decrypt_data($value);
            },
            'last_name' => function($value, $item) {
                if (empty ($value) && empty ($item['first_name'])) {
                    $value = empty ($item['last_name_kana']) ? '' : $item['last_name_kana'];
                }

                return decrypt_data($value);
            },

            '$member_type' => function ($item) {
                return $item['user_status'] ? '会員' : '非会員';
            },
            'delete_reason' => function ($deletedReason, $item) {
                return empty ($item['status'] == self::STATUS_INVALID) ? '' : $deletedReason;
            },
            'sent_received_msg_at' => function ($value) {
                if (!$value) {
                    return false;
                }
                $t = strtotime($value);
                return date('m/d', $t) . '<br>' . date('H:i', $t);

            },
            'sent_prepared_msg_at' => function ($value) {
                if (!$value) {
                    return false;
                }


                $t = strtotime($value);
                return date('m/d', $t) . '<br>' . date('H:i', $t);
            },
            'sent_other_msg_at' => function ($value) {
                if (!$value) {
                    return false;
                }


                $t = strtotime($value);
                return date('m/d', $t) . ' ' . date('H:i', $t);
            },
        );
    }

}