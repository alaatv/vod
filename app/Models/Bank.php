<?php

namespace App\Models;


class Bank extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];

    public function backaccounts()
    {
        return $this->hasMany(Bankaccount::class);
    }
}
