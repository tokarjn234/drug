<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;



class MessageTemplate extends AppModel
{
    protected $table = 'message_templates';
    const MSG_TYPE_RECEIVED_NOTIFY  = 0;
    const MSG_TYPE_PREPARED_NOTIFY  = 1;
    const MSG_TYPE_OTHER_NOTIFY  = 2;
    const MSG_TYPE_NOTICE = 3;
    const MSG_TYPE_OTHER = 4;

    const TYPE_GROUP = 1;
    const TYPE_COMPANY = 2;
    const TYPE_STORE = 3;

    const STATUS_UNUSED = 0;
    const STATUS_APPLIED = 1;
    const STATUS_TEMP_REGISTER = 2;
    const STATUS_DELETE = 3;

    public static $messageTypes = [
        self::MSG_TYPE_RECEIVED_NOTIFY => '受付通知',
        self::MSG_TYPE_PREPARED_NOTIFY => '調剤完了',
        self::MSG_TYPE_OTHER_NOTIFY => 'その他通知',
        self::MSG_TYPE_NOTICE => 'お知らせ',
        self::MSG_TYPE_OTHER => 'その他'

    ];

    public static $messageStatus = [
        self::STATUS_UNUSED => '未使用',
        self::STATUS_APPLIED => '適用中',
        self::STATUS_TEMP_REGISTER => '仮登録',
        self::STATUS_DELETE => '削除'
    ];

    public $fillable = [
        'id','name','store_id','company_id','alias','message_type','type','status','title','content','update_staff_id'
    ];

    /**
     * Gets default message templates
     * @param $storeId
     * @param $companyId
     * @return array
     */
    public static function getDefaultMessagesTemplates($storeId, $companyId) {
        $SettingsTab1Clone = MessageTemplate::where('type', MessageTemplate::TYPE_COMPANY)
            ->where('store_id', $storeId)
            ->where('company_id', $companyId)
            ->whereNotNull('copy_from')
            ->lists('copy_from');

        $receivedMsg = MessageTemplate::select('id', 'name', 'title', 'content')
            ->where('type', MessageTemplate::TYPE_COMPANY)
            ->where('status', MessageTemplate::STATUS_APPLIED)
            ->where('message_type', '=', self::MSG_TYPE_RECEIVED_NOTIFY)
            ->where(function($query) use ($SettingsTab1Clone, $storeId, $companyId) {
                return $query->where(function($query) use ($SettingsTab1Clone){
                    return $query->whereNull('copy_from')
                        ->whereNull('company_id')
                        ->where('status', MessageTemplate::STATUS_APPLIED)
                        ->whereNotIn('id', $SettingsTab1Clone);
                })->Orwhere(function($query) use ($storeId, $companyId) {
                    return $query->whereNotNull('copy_from')
                        ->where('store_id', $storeId)
                        ->where('company_id', $companyId);
                });
            })->first();

        $preparedMsg = MessageTemplate::select('id', 'name', 'title', 'content')
            ->where('type', MessageTemplate::TYPE_COMPANY)
            ->where('status', MessageTemplate::STATUS_APPLIED)
            ->where('message_type', '=', self::MSG_TYPE_PREPARED_NOTIFY)
            ->where(function($query) use ($SettingsTab1Clone, $storeId, $companyId) {
                return $query->where(function($query) use ($SettingsTab1Clone){
                    return $query->whereNull('copy_from')
                        ->whereNull('company_id')
                        ->where('status', MessageTemplate::STATUS_APPLIED)
                        ->whereNotIn('id', $SettingsTab1Clone);
                })->Orwhere(function($query) use ($storeId, $companyId) {
                    return $query->whereNotNull('copy_from')
                        ->where('store_id', $storeId)
                        ->where('company_id', $companyId);
                });
            })->first();

        if (empty ($receivedMsg)) {
            $receivedMsg = (object)[
                'title' => 'ReceivedMessage',
                'content' => 'ReceivedMessageContent'
            ];
        }

        if (empty ($preparedMsg)) {
            $preparedMsg= (object) [
                'title' => 'PreparedMessage',
                'content' => 'PreparedMessageContent'
            ];
        }

        $receivedMsg->header = __('ReceivedMessagePopupHeader');
        $preparedMsg->header = __('PreparedMessagePopupHeader');


        return [
            'received' => $receivedMsg,
            'prepared' => $preparedMsg
        ];

    }


    public static function getAllMessagesTemplates($storeId, $companyId) {
        $SettingsClone = MessageTemplate::whereNotNull('copy_from')->where(function($query) use ($storeId){
            return $query->where('store_id', $storeId)
                ->orWhereNull('store_id');
        })->lists('copy_from');

        $SettingsNotCurrentStore = MessageTemplate::where('type', MessageTemplate::TYPE_GROUP)
            ->where('store_id','!=', $storeId)
            ->where('company_id', $companyId)
            ->lists('id');

        return MessageTemplate::leftJoin('staffs', function($join) {
            $join->on('message_templates.update_staff_id', '=', 'staffs.id');
        })
            ->select('staffs.*','message_templates.*')
            ->whereNotIn('message_templates.id', $SettingsClone)
            ->whereNotIn('message_templates.id', $SettingsNotCurrentStore)
            ->where('message_templates.status', MessageTemplate::STATUS_APPLIED)
            ->where(function($query) use ($storeId, $companyId) {
                return $query->where(function($query) use ($companyId) {
                    return $query->whereNull('message_templates.copy_from')
                        ->where(function($query) use ($companyId) {
                            return $query->where('message_templates.company_id', $companyId)
                                        ->orWhereNull('message_templates.company_id');
                        })
                        ->where('message_templates.type', '!=', MessageTemplate::TYPE_STORE);
                })->Orwhere(function($query) use ($storeId, $companyId) {
                    return $query->where('message_templates.store_id', $storeId)
                        ->where('message_templates.company_id', $companyId);
                });
            })->get();
    }



    /**
     * Gets default message templates
     * @param $storeId
     * @param $companyId
     * @return array
     */
    public static function getDefaultMessageTemplates($storeId, $companyId) {

        $SettingsTab1Clone = MessageTemplate::where('type', MessageTemplate::TYPE_COMPANY)
            ->where('store_id', $storeId)
            ->where('company_id', $companyId)
            ->whereNotNull('copy_from')
            ->lists('copy_from');

        $receivedMsg = MessageTemplate::select('id', 'name', 'title', 'content')
            ->where('type', MessageTemplate::TYPE_COMPANY)
            ->where('status', MessageTemplate::STATUS_APPLIED)
            ->where('message_type', '=', self::MSG_TYPE_RECEIVED_NOTIFY)
            ->where(function($query) use ($SettingsTab1Clone, $storeId, $companyId) {
                return $query->where(function($query) use ($SettingsTab1Clone){
                    return $query->whereNull('copy_from')
                        ->whereNull('company_id')
                        ->where('status', MessageTemplate::STATUS_APPLIED)
                        ->whereNotIn('id', $SettingsTab1Clone);
                })->Orwhere(function($query) use ($storeId, $companyId) {
                    return $query->whereNotNull('copy_from')
                        ->where('store_id', $storeId)
                        ->where('company_id', $companyId);
                });
            })->first();

        $preparedMsg = MessageTemplate::select('id', 'name', 'title', 'content')
            ->where('type', MessageTemplate::TYPE_COMPANY)
            ->where('status', MessageTemplate::STATUS_APPLIED)
            ->where('message_type', '=', self::MSG_TYPE_PREPARED_NOTIFY)
            ->where(function($query) use ($SettingsTab1Clone, $storeId, $companyId) {
                return $query->where(function($query) use ($SettingsTab1Clone){
                    return $query->whereNull('copy_from')
                        ->whereNull('company_id')
                        ->where('status', MessageTemplate::STATUS_APPLIED)
                        ->whereNotIn('id', $SettingsTab1Clone);
                })->Orwhere(function($query) use ($storeId, $companyId) {
                    return $query->whereNotNull('copy_from')
                        ->where('store_id', $storeId)
                        ->where('company_id', $companyId);
                });
            })->first();

        if (empty ($receivedMsg)) {
            $receivedMsg = (object)[
                'title' => 'ReceivedMessage',
                'content' => 'ReceivedMessageContent'
            ];
        }

        if (empty ($preparedMsg)) {
            $preparedMsg= (object) [
                'title' => 'PreparedMessage',
                'content' => 'PreparedMessageContent'
            ];
        }

        $receivedMsg->header = __('ReceivedMessagePopupHeader');
        $preparedMsg->header = __('PreparedMessagePopupHeader');


        return [
            'received' => $receivedMsg,
            'prepared' => $preparedMsg
        ];
    }
}

