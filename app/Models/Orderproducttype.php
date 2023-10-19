<?php

namespace App\Models;

class Orderproducttype extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'displayName',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
