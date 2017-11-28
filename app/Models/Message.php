<?php


namespace App\Models;



class Message extends  AppModel
{
    const TARGET_USER_TO_STORE = 0;
    const TARGET_STORE_TO_USER = 1;
    const TARGET_STORE_TO_ALL_USERS = 2;
    const TARGET_COMPANY_TO_ALL_COMPANY_MEMBERS = 3;
    const TARGET_SYSTEM_TO_ALL_USERS = 4;

    const MSG_ORDERING_BY_ID = 1;
    const MSG_ORDERING_BY_LATEST_MSG = 2;

    const MSG_TYPE_ACCEPT  = 0;
    const MSG_TYPE_COMPLET = 1;
    const MSG_TYPE_ORTHER  = 2;

    public $fillable = [
        'order_id', 'user_id', 'store_id', 'company_id', 'template_id', 'title', 'content', 'type', 'target', 'created_staff_id', 'status'
    ];

    public  function  store(){
        return $this->belongsTo('App\Models\Store');
    }

    public  function  order(){
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * @param  string  $value
     * @return string
     */
    public  function getTitleAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string  $value
     * @return string
     */
    public  function getContentAttribute($value)
    {
        return decrypt_data($value);
    }

    /**
     * @param  string  $value
     * @return string
     */
    public  function setTitleAttribute($value)
    {
        $this->attributes['title'] = encrypt_data($value);

    }

    /**
     * @param  string  $value
     * @return string
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = encrypt_data($value);
    }

}