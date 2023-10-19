<?php

namespace App\Models;

class Paymentstatus extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',

    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
