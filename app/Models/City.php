<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class City extends AppModel
{
    protected $table = 'cities_master';
    public function province()
    {
        return $this->belongsTo('App\Models\Province');
    }
    public function district()
    {
        return $this->hasMany('App\Models\District');
    }
    public function store()
    {
        return $this->hasMany('App\Models\Store');
    }

}