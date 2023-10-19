<?php

namespace App\Models;

class Orderstatus extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
