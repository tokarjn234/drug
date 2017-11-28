<?php

namespace App\Models;


class PasswordStaff extends AppModel
{
    protected $table = 'password_staffs';
    protected $fillable = [
        'password', 'staff_id', 'times', 'last_change_password', 'type',
    ];
    const ACC_TYPE_USER = 0;
    const ACC_TYPE_EMPLOYEE_STORE = 1;
    const ACC_TYPE_MANAGEMENT = 2;
    const ACC_TYPE_MEDIAID = 3;

}