<?php

namespace App\Models;


class Coupontype extends BaseModel
{
    public const ATTRIBUTE_TYPE_OVERALL_ID = 1;
    public const ATTRIBUTE_TYPE_PARTIAL_ID = 2;
    public const ATTRIBUTE_TYPE_OVERALL = 'overall';
    public const ATTRIBUTE_TYPE_PARTIAL = 'partial';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'displayName',
        'description',
    ];

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }
}
