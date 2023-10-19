<?php

namespace App\Models;

use Carbon\Carbon;
use stdClass;

class Subscription extends BaseModel
{
    protected $fillable = [
        'order_id',
        'user_id',
        'subscription_id',
        'subscription_type',
        'valid_since',
        'valid_until',
        'values',
        'seller',
    ];

    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    public function order()
    {
        return $this->belongsTo(Order::Class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function subscription()
    {
        return $this->morphTo();
    }

    public function getValuesAttribute($value)
    {
        return json_decode($value);
    }

    public function setValuesAttribute(stdClass|array $values = null)
    {
        $this->attributes['values'] = json_encode($values);
    }

    public function scopeNotExpired($query)
    {
        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran'));

        $query->where(function ($q) use ($now) {
            $q->where('valid_since', '<', $now)
                ->orWhereNull('valid_since');
        })
            ->where(function ($q) use ($now) {
                $q->where('valid_until', '>', $now)
                    ->orWhereNull('valid_until');
            });
    }

    public function setUsageLimit(int $usageLimit): void
    {
        $values = $this->values;
        if (isset($values->discount)) {
            $values->discount->usage_limit = $usageLimit;
        }

        $this->values = $values;
    }

    public function setOrderproductId(int $orderproductId): void
    {
        $values = $this->values;
        if (isset($values->discount)) {
            $values->discount->orderproduct_id = [$orderproductId];
        }

        $this->values = $values;
    }

    public function unsetOrderproductId(): void
    {
        $values = $this->values;
        if (isset($values->discount)) {
            $values->discount->orderproduct_id = [];
        }

        $this->values = $values;
    }

    public function getDiscountAttribute()
    {
        return $this->values?->discount ?? 0;
    }

    public function getDiscountAmountAttribute()
    {
        $values = $this->values;

        if (!isset($values)) {
            return 0;
        }

        $discount = $values->discount;
        if (!isset($discount)) {
            return 0;
        }

        return $discount->discount_amount ?? 0;
    }

    public function getUsageLimitAttribute()
    {
        $values = $this->values;

        if (!isset($values)) {
            return null;
        }

        $discount = $values->discount;
        if (!isset($discount)) {
            return null;
        }

        return $discount->usage_limit;
    }

    public function getOrderproductsattribute()
    {
        $values = $this->values;

        if (!isset($values)) {
            return null;
        }

        $discount = $values->discount;
        if (!isset($discount)) {
            return null;
        }

        return $discount->orderproduct_id ?? [];
    }

    public function disable()
    {
        $values = $this->values;

        $values->active = false;
        $values->discount->usage_limit = 0;

        $this->values = $values;
        $this->save();
    }
}
