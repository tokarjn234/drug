<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class District extends AppModel
{

    protected $table = 'districts_master';
    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }
    public function store()
    {
        return $this->hasMany('App\Models\Store');
    }
    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }


}