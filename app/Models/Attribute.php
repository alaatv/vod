<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;


class Attribute extends BaseModel
{
    public const SUBSCRIPTION_DURATION_MAP = [];
    public const SUBSCRIPTION_DURATION_ATTRIBUTE = 50;
    public const SUBSCRIPTION_WALLET_ATTRIBUTE = 53;
    public const TIMEPOINT_ATTRIBUTE_NAME = 'timepoint';
    public const DISCOUNT_ATTRIBUTE_NAME = 'discount';
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
        'attributecontrol_id',
        'attributetype_id',
    ];

    public function attributegroups()
    {
        return $this->belongsToMany(Attributegroup::class)
            ->withTimestamps();
    }

    public function attributecontrol()
    {
        return $this->belongsTo(Attributecontrol::class);
    }

    public function attributetype()
    {
        return $this->belongsTo(Attributetype::class);
    }

    /**
     * @return HasMany|Attributevalue
     */
    public function attributevalues()
    {
        return $this->hasMany(Attributevalue::class);
    }

    /**
     * Combine Attribute items: id, displayName
     *
     * @return string
     */
    public function getIdInfoAttribute()
    {
        return "{$this->id} - {$this->displayName}";
    }
}
