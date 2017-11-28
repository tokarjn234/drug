<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AccessLog extends AppModel
{
    protected $table = 'access_logs';
    protected $timestamp = true;
    protected $fillable = ['timestamp', 'IP', 'sourcetype', 'target', 'account_id', 'access_data_type', 'access_data_id', 'is_auto_login', 'access_function', 'access_action', 'access_result', 'access_data_debug'];
    const SUCCESS_ACCESS_LOG = 0;
    const FAIL_ACCESS_LOG = 1;

    const SUCCESS_AUTO_LOGIN = 1;
    const FAIL_AUTO_LOGIN = 0;
}