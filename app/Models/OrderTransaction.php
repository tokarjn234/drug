<?php


namespace App\Models;


class OrderTransaction extends AppModel
{
    protected $table = 'order_transaction';
    public function order()
    {
        return $this->hasOne('App\Models\Order', 'id', 'id');
    }

}