<?php
namespace App\Http\Controllers\Mediaid;

use App\Http\Controllers\AppController;
use Auth;

class MediaidAppController extends AppController
{
    /**
     * @param $field
     * @return \App\Models\Staff
     */
    protected function getCurrentStaff($field = null) {
        return \App\Models\Staff::current($field);
    }


}