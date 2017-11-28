<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\AppController;
use Auth;

class CompanyAppController extends AppController
{


    /**
     * @param $field
     * @return \App\Models\Company
     */

    protected function getCurrentCompany($field = null) {
        return \App\Models\Company::current($field);
    }

    /**
     * @param $field
     * @return \App\Models\Staff
     */
    protected function getCurrentStaff($field = null) {
        return \App\Models\Staff::current($field);
    }

}