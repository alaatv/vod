<?php

namespace App\Models;

class Paymentmethod extends BaseModel
{
    public const ONLINE_ID = 1;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
