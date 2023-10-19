<?php

namespace App\Models;


class Wallettype extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
}
