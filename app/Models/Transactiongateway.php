<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Transactiongateway extends BaseModel
{
    public const GATE_WAY_ZARINPAL_ID = 1;
    public const GATE_WAY_ENBANK_ID = 2;
    public const GATE_WAY_MELLAT_ID = 4;
    public const GATE_WAY_PARSIAN_ID = 5;
    public const GATE_WAY_SAMAN_ALAA_ID = 6;
    public const GATE_WAY_SAMAN_SOALAA_ID = 7;
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
        'merchantNumber',
        'enable',
        'order',
        'bank_id',
        'merchantPassword',
        'certificatePrivateKeyFile',
        'certificatePrivateKeyPassword',
        'url',
        'icon',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope a query to only include enable(or disable) Gateways.
     *
     * @param  Builder  $query
     * @param  int  $enable
     *
     * @return Builder
     */
    public function scopeEnable($query, int $enable = 1)
    {
        return $query->where('enable', $enable);
    }

    /**
     *  Scope a query to only include Gateways with specified name.
     *
     * @param  Builder  $query
     * @param  string  $name
     *
     * @return Builder
     */
    public function scopeName($query, string $name)
    {
        return $query->where('name', $name);
    }

    public function getIconUrlAttribute()
    {
        return '/acm/extra/payment/gateway/'.$this->icon;
    }
}
