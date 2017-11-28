<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Province extends AppModel
{
    protected $table = 'province_master';
    public function city()
    {
        return $this->hasMany('App\Models\City');
    }
}