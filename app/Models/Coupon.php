<?php

namespace App\Models;

use App\Traits\logger;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | Traits
    |--------------------------------------------------------------------------
    */
    use HasFactory;
    use logger;

    public const LOG_ATTRIBUTES = [
        'coupontype_id',
        'discounttype_id',
        'name',
        'enable',
        'code',
        'usageLimit',
        'usageNumber',
        'validSince',
        'validUntil',
    ];

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */
    public const COUPON_VALIDATION_STATUS_OK = 0;
    public const COUPON_VALIDATION_STATUS_DISABLED = 1;
    public const COUPON_VALIDATION_STATUS_USAGE_TIME_NOT_BEGUN = 2;
    public const COUPON_VALIDATION_STATUS_EXPIRED = 3;
    public const COUPON_VALIDATION_STATUS_USAGE_LIMIT_FINISHED = 4;
    public const COUPON_VALIDATION_STATUS_NOT_FOUND = 5;
    public const ARASH_COUPON_NAME = 'ویژه آرش';
    public const ARASH_COUPON_DISCOUNT = 50;
    public const REHE_ABRISHAM_BUYERS_COUPON = 'acfb';
    public const ARASH_ARABI_BUYERS = 'uryt';
    public const CLOZE_TAFTAN_BUYERS = 'clozeTaftan';
    public const HEKMAT_50_COUPON_ID = 6474; //For Godar98 and Taftan99 packages
    public const HEKMAT_40_COUPON_ID = 6478; //For Arash99 and Abrisham98 packages
    public const PARCHAM_COUPON = 'parcham';
    public const TAFTAN1400_FOR_ABRISHAM_COUPON = 'rah80';
    public const ARASH1400_OMOOMI_FOR_ABRISHAM_COUPON = 'OA75';
    public const ARASH1400_TAKHASOSI_FOR_ABRISHAM_COUPON = 'EA75';
    public const ARASH_FIZIK_1400_TOLOUYI_COUPON = 'TP50';
    public const MARKETNG_AKHARIN_FORSAT_2 = 'AP1401';
    public const _3A_ABAN = '3َA50';
    public const TOOR_ABRISHAM_PACK_1400 = 'TRP40';
    public const TOOR_ABRISHAM_TAK_1400 = 'TRT40';
    public const TOOR_ABRISHAM_WINTER = 'TR60';
    public const BONYAD_EHSAN_COUPON = 174452;
    public const TETA_SHIMI_FOR_RAHE_ABRISHAM = 'TTRS';
    public const TETA_SHIMI_FOR_TOORE_ABRISHAM = 'TTUS';
    public const TETA_ADABIYAT_FOR_RAHE_ABRISHAM = 'TTRA';
    public const ROOZE_PEDAR_A400_FOR_RAHE_ABRISHAM = 'RP60';
    public const ROOZE_PEDAR_A400_FOR_TOORE_ABRISHAM = 'UP50';
    public const TAFTAN1400_FOR_ABRISHAM_COUPON_2 = 'TFR70';
    public const TAFTAN1400_FOR_ABRISHAM_COUPON_3 = 'TFR30';

    public const COUPON_VALIDATION_INTERPRETER = [
        self::COUPON_VALIDATION_STATUS_DISABLED => 'Coupon is disabled',
        self::COUPON_VALIDATION_STATUS_USAGE_LIMIT_FINISHED => 'Coupon number is finished',
        self::COUPON_VALIDATION_STATUS_EXPIRED => 'Coupon is expired',
        self::COUPON_VALIDATION_STATUS_USAGE_TIME_NOT_BEGUN => 'Coupon usage period has not started',
        self::COUPON_VALIDATION_STATUS_NOT_FOUND => 'Coupon not found'
    ];

    public const INDEX_PAGE_NAME = 'couponPage';

    public const GENERATE_RANDOM_CODE_THRESHOLD = 10;

    public const PARCHAM_COUPON_ID = 8526;
    public const PARCHAM_COUPON_1401_90 = 16022;
    public const PARCHAM_COUPON_1401_50 = 16023;
    public const PARCHAM_COUPON_1401_100 = 16024;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'enable',
        'required_products',
        'unrequired_products',
        'description',
        'code',
        'discount',
        'maxCost',
        'usageLimit',
        'usageNumber',
        'validSince',
        'validUntil',
        'coupontype_id',
        'discounttype_id',
        'hasPurchased',
        'is_strict',
    ];
    protected $appends = [
        'couponType',
        'discountType',
        'edit_link',
        'remove_link',
    ];
    protected $hidden = [
//        'id',
//        'enable',
//        'maxCost',
//        'usageLimit',
//        'usageNumber',
//        'validSince',
//        'validUntil',
//        'created_at',
        'updated_at',
        'deleted_at',
        'coupontype_id',
        'discounttype_id',
    ];
    protected $casts = [
        'required_products' => 'array',
        'unrequired_products' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public static function isOK(int $couponValidationStatus): bool
    {
        return $couponValidationStatus == self::COUPON_VALIDATION_STATUS_OK;
    }

    public static function isFinished(int $couponValidationStatus): bool
    {
        return $couponValidationStatus == self::COUPON_VALIDATION_STATUS_USAGE_LIMIT_FINISHED;
    }

    public function marketers()
    {
        return $this->belongsToMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function purchasedOrderproducts()
    {
        return $this->belongsToMany(Orderproduct::Class, 'orderproduct_purchasedcoupons', 'coupon_id',
            'orderproduct_id');
    }

    /**
     * Scope a query to only include enable(or disable) Coupons.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeEnable($query)
    {
        return $query->where('enable', '=', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include valid Coupons.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeValid($query)
    {
        $now = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran'));

        return $query->where(function ($q) use ($now) {
            $q->where('validSince', '<', $now)
                ->orWhereNull('validSince');
        })
            ->where(function ($q) use ($now) {
                $q->where('validUntil', '>', $now)
                    ->orWhereNull('validUntil');
            });
    }

    public function scopeUsageLeft($query)
    {
        return $query->whereNull('usageLimit')->orWhereRaw('coupons.usageLimit >= coupons.usageNumber');
    }

    /*
    |--------------------------------------------------------------------------
    | Others
    |--------------------------------------------------------------------------
    */

    /**
     * Validates a coupon
     *
     * @return int
     */
    public function validateCoupon()
    {

        if ($this->hasTotalNumberFinished()) {
            return self::COUPON_VALIDATION_STATUS_USAGE_LIMIT_FINISHED;
        }
        if (!$this->isEnable()) {
            return self::COUPON_VALIDATION_STATUS_DISABLED;
        }
        if (!$this->hasPassedSinceTime()) {
            return self::COUPON_VALIDATION_STATUS_USAGE_TIME_NOT_BEGUN;
        }
        if (!$this->hasTimeToUntilTime()) {
            return self::COUPON_VALIDATION_STATUS_EXPIRED;
        }

        return self::COUPON_VALIDATION_STATUS_OK;
    }

    /**
     * Determines whether this coupon total number has finished or not
     *
     * @return bool
     */
    public function hasTotalNumberFinished(): bool
    {
        return isset($this->usageLimit) && $this->usageNumber >= $this->usageLimit;
    }

    /**
     * Determines whether this coupon is enabled or not
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable ? true : false;
    }

    /**
     * Determines whether this coupon usage time has started or not
     *
     * @return bool
     */
    public function hasPassedSinceTime(): bool
    {
        return !isset($this->validSince) || Carbon::now()
                ->setTimezone('Asia/Tehran') >= $this->validSince;
    }

    /**
     * Determines whether this coupon usage time has ended or not
     *
     * @return bool
     */
    public function hasTimeToUntilTime(): bool
    {
        return !isset($this->validUntil) || Carbon::now()
                ->setTimezone('Asia/Tehran') <= $this->validUntil;
    }

    public function isPercentageType(): bool
    {
        return $this->discounttype_id == config('constants.DISCOUNT_TYPE_PERCENTAGE');
    }

    public function isAmountType(): bool
    {
        return $this->discounttype_id == config('constants.DISCOUNT_TYPE_COST');
    }

    public function getCouponTypeAttribute()
    {
        return optional($this->coupontype()
            ->first())->setVisible([
            'name',
            'displayName',
            'description',
        ]);
    }

    public function coupontype()
    {
        return $this->belongsTo(Coupontype::class);
    }

    /**
     * Determines whether this coupon has the passed product or not
     *
     * @param  Product  $product
     *
     * @return bool
     */
    public function hasProduct(Product $product): bool
    {

        if (in_array($product->id, [Product::CUSTOM_DONATE_PRODUCT, Product::DONATE_PRODUCT_5_HEZAR])) {
            return false;
        }

        $flag = true;
        if ($this->coupontype->id == config('constants.COUPON_TYPE_PARTIAL')) {
            $couponProducts = $this->products;
            $flag = $couponProducts->contains($product);
        }

        return $flag;
    }

    public function decreaseUseNumber(): self
    {
        $this->usageNumber = max($this->usageNumber - 1, 0);
        return $this;
    }

    public function increaseUseNumber(): self
    {
        $this->usageNumber++;
        return $this;
    }

    public function encreaseUserNumber()
    {
        $this->usageNumber++;
    }

    public function getDiscountTypeAttribute()
    {
        return optional($this->discounttype()
            ->first())
            ->setVisible([
                'name',
                'displayName',
                'description',
            ]);
    }

    public function discounttype()
    {
        return $this->belongsTo(Discounttype::class);
    }

    /**
     * @return string
     */
    public function getEditLinkAttribute(): string
    {
        return route('coupon.edit', $this->id);
    }

    public function getRemoveLinkAttribute(): string
    {
        return action('Web\CouponController@destroy', $this->id);
    }

    public function productVouchers()
    {
        return $this->hasMany(Productvoucher::class);
    }
}
