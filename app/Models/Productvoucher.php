<?php

namespace App\Models;

use App\Collection\VoucherCollections;
use App\Traits\DateTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Productvoucher extends BaseModel
{
    use DateTrait;
    use HasFactory;

    public const CONTRANCTOR_ASIATECH = 1;
    public const CONTRANCTOR_HEKMAT = 2;

    /**
     * @var array
     */
    protected $fillable = [
        'contractor_id',
        'product_id',
        'products',
        'user_id',
        'order_id',
        'used_at',
        'code',
        'package_name',
        'expirationdatetime',
        'enable',
        'description',
        'coupon_id'
    ];

    public function newCollection(array $models = [])
    {
        return new VoucherCollections($models);
    }

    public function getProductsAttribute($value)
    {
        if (is_null($value)) {
            return collect();
        }

        return Product::whereIn('id', json_decode($value))->get();
    }

    public function setProductsAttribute($value)
    {
        if (is_array($value)) {
            $value = '[ '.implode(',', $value).' ]';
        }
        $this->attributes['products'] = $value;
    }


    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::Class);
    }

    public function isValid()
    {
        return $this->isEnable() && !$this->isExpired();
    }

    public function isEnable()
    {
        return $this->enable ? true : false;
    }

    public function isExpired()
    {
        return $this->expirationdatetime < Carbon::now('Asia/Tehran');
    }

    public function hasBeenUsed()
    {
        return !is_null($this->user_id);
    }

    public function markVoucherAsUsed(int $userId, int $orderId, int $contractor_id)
    {
        return $this->update([
            'user_id' => $userId,
            'order_id' => $orderId,
            'used_at' => Carbon::now('Asia/Tehran'),
            'contractor_id' => $contractor_id,
        ]);
    }

    public function scopeSearch($query, $keywords)
    {
        $keywords = explode(' ', $keywords);
        foreach ($keywords as $keyword) {
            $query->where('package_name', 'LIKE', '%'.$keyword.'%')
                ->orWhere('code', 'LIKE', '%'.$keyword.'%')
                ->orWhere('description', 'LIKE', '%'.$keyword.'%');
        }
        return $query;
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
