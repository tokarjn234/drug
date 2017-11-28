<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LoginLog extends AppModel
{
    protected $table = 'login_logs';
    protected $timestamp = true;
    protected $fillable = ['timestamp', 'IP', 'sourcetype', 'target', 'input_id', 'login_result', 'account_type', 'account_id'];

    const SUCCESS_LOGIN_LOG = 0;
    const FAIL_LOGIN_LOG = 1;
    const SUCCESS_AUTO_LOGIN_LOG = 2;
    const FAIL_AUTO_LOGIN_LOG = 3;
}