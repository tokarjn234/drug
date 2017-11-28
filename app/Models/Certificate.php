<?php

namespace App\Models;


class Certificate extends AppModel implements IDataRender
{
    use TDataRender;

    public $table = 'certificates';

    const STATUS_NOT_DIVIDE = 0;
    const STATUS_DIVIDED_TO_STORE = 1;
    const STATUS_DIVIDED_TO_DEVICE = 2;
    const STATUS_INACTIVE = 3;
    const STATUS_ALL = -1;
    const IS_NOT_MEDIAID = 0;
    const IS_MEDIAID = 1;

    public static $statuses = [
        self::STATUS_ALL => 'すべて',
        self::STATUS_NOT_DIVIDE => '未割当',
        self::STATUS_DIVIDED_TO_STORE => '割当済',
        self::STATUS_DIVIDED_TO_DEVICE => '導入済',
        self::STATUS_INACTIVE => '無効'
    ];

    public static function getCert($code, $field = null)
    {
        $cert = Certificate::where('ssl_client_s_dn_cn', $code)->first();
        if (!$cert) {
            return null;
        }
        if (!$field) {
            return $cert;
        }

        return @$cert->{$field};
    }

    public static function getRenderSettings()
    {
        return [
            'name' => function ($value, $item) {
                if ($item['status'] == self::STATUS_NOT_DIVIDE || $item['status'] == self::STATUS_DIVIDED_TO_STORE) {
                    return '-';
                }

                return $value;
            },
            'store_name' => function ($value, $item) {
                if ($item['status'] == self::STATUS_NOT_DIVIDE) {
                    return '-';
                }

                return $value;
            },
            'store_id' => function ($value, $item) {
                if ($item['status'] == self::STATUS_NOT_DIVIDE) {
                    return '-';
                }

                return $value;
            },
            'issued_to_store_at' => function ($value, $item) {
                if ($item['status'] == self::STATUS_NOT_DIVIDE) {
                    return '-';
                }

                return $value ? date('Y/m/d', strtotime($value)) : '';
            },
            'created_at' => function ($value) {
                return $value ? date('Y/m/d', strtotime($value)) : '';
            },

            'updated_at' => function ($value) {
                return $value ? (date('Y/m/d', strtotime($value)) . '<br>' . date('H:i', strtotime($value))) : '';
            },
            '$status' => function ($cer) {
                if (empty ($cer['status'])) {
                    $cer['status'] = self::STATUS_NOT_DIVIDE;
                }

                return @self::$statuses[$cer['status']];
            }
        ];
    }


}