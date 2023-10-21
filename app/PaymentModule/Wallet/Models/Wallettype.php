<?php

namespace App\PaymentModule\Wallet\Models;

use App\Models\BaseModel;


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
