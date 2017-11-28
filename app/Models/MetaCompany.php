<?php
/**
 * Created by PhpStorm.
 * User: Lenh Ho Xung
 * Date: 07/03/2016
 * Time: 11:05 SA
 */

namespace App\Models;


class MetaCompany extends AppModel
{
    protected $table = 'meta_companies';
    protected $fillable = [
        'division', 'name_division', 'phone_number', 'fax_division', 'company_id',
    ];

}