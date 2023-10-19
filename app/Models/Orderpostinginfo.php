<?php

namespace App\Models;


class Orderpostinginfo extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'postCode',
    ];

    public function order()
    {
        return $this->belongsTo(Order::Class);
    }
}
