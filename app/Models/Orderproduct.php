<?php

namespace App\Models;

use App\Classes\Checkout\Alaa\OrderproductCheckout;
use App\Collection\OrderproductCollection;
use App\Repositories\OrderproductRepo;
use App\Traits\Helper;
use App\Traits\ProductCommon;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Orderproduct extends BaseModel
{
    use HasFactory;
    use Helper;
    use ProductCommon;

    /**
     * @var array
     */
    protected $fillable = [
        'orderproducttype_id',
        'order_id',
        'product_id',
        'quantity',
        'cost',
        'tmp_final_cost',
        'tmp_extra_cost',
        'tmp_share_order',
        'discountPercentage',
        'discountAmount',
        'expire_at',
        'includedInCoupon',
        'instalmentQty',
        'paidPercent',
        'checkoutstatus_id',
        'includedInInstalments',
        'financial_category_id',
    ];

    protected $casts = [
        'instalmentQty' => 'array'
    ];

    protected $touches = [
        // ToDo: Query reduction
        /**
         * Ali Esmaeeli: in OrderProductController@store create 8 query
         * To comment this line, you need to find all the places where
         * the orderProduct has been changed and clear the cache
         */
        'attributevalues',
    ];

    protected $appends = [
        'orderproducttype',
        'product',
        'grandId',
        'price',
        'bons',
        'attributevalues',
        'photo',
        'grandProduct',
        'purchased_coupon_code'
    ];

    protected $hidden = [
        'product_id',
        'cost',
        'discountPercentage',
        'discountAmount',
        'orderproducttype_id',
        'checkoutstatus_id',
        'includedInCoupon',
        'userbons',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function deleteOpenedTransactions(array $intendedProductsId, array $intendedOrderStatuses): void
    {
        Orderproduct::whereIn('product_id', $intendedProductsId)
            ->whereHas('order', function ($q) use ($intendedOrderStatuses) {
                $q->whereIn('orderstatus_id', $intendedOrderStatuses)
                    ->whereDoesntHave('transactions', function ($q2) {
                        $q2->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_TRANSFERRED_TO_PAY'));
                    });
            })
            ->delete();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function commission()
    {
        return $this->hasOne(UserCommission::class, 'orderProduct_id');
    }

    public function purchasedCoupons()
    {
        return $this->belongsToMany(Coupon::Class, 'orderproduct_purchasedcoupons', 'orderproduct_id', 'coupon_id');
    }

    public function lockReasons()
    {
        return $this->belongsToMany(ReasonOfLockedOrderproduct::class, 'lockedOrderproducts', 'orderproduct_id',
            'reason_lock_id');
    }

    public function getOrderproducttypeAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:type:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'orderproductType', 'order_'.$orderproduct->id,
            'order_'.$orderproduct->id.'_orderproductType'
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($orderproduct) {
                return optional($this->orderproducttype()
                    ->first())->setVisible([
                    'name',
                    'displayName',
                ]);
            });
    }

    public function cacheKey()
    {
//        if(is_null($this->created_at) && is_null($this->updated_at))
//        {
//            Log::channel('debug')->info('OrderProduct:'.$this->id.'has not created_at and updated_at');
//        }

        $key = $this->getKey();
        $time = $this->updated_at->timestamp ?? $this->created_at->timestamp ?? $this->id;

        return sprintf('%s-%s', //$this->getTable(),
            $key, $time);
    }


    public function orderproducttype()
    {
        return $this->belongsTo(Orderproducttype::class);
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     *
     * @return Collection
     */
    public function newCollection(array $models = [])
    {
        return new OrderproductCollection($models);
    }

    public function insertedUserbons()
    {
        return $this->hasMany(Userbon::class);
    }

    public function scopeExpired($query)
    {
        return $query->where('expire_at', '<', Carbon::now());
    }

    public function scopeReferralCodeOrderProducts($query)
    {
        return $query->whereNotIn('product_id', Product::DONATE_PRODUCT_ARRAY)
            ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->wherehas('order.referralCode', function ($query) {
                $query->wherehas('referralRequest', function ($query) {
                    $query->where('owner_id', auth()->id());
                });
            })->wherehas('order', function ($query) {
                $query->paidAndClosed();
            });
    }

    public function renewals()
    {
        return $this->hasMany(OrderProductRenewal::class);
    }

    public function checkoutstatus()
    {
        return $this->belongsTo(Checkoutstatus::class);
    }

    public function children()
    {
        return $this->belongsToMany(Orderproduct::class, 'orderproduct_orderproduct', 'op1_id',
            'op2_id')
            ->withPivot('relationtype_id')
            ->join('orderproductinterrelations', 'relationtype_id',
                'orderproductinterrelations.id')
            ->where('relationtype_id', config('constants.ORDER_PRODUCT_INTERRELATION_PARENT_CHILD'));
    }

    public function getExtraCost($extraAttributevaluesId = null): int
    {
        $orderproduct = $this;
        $key =
            'orderproduct:getExtraCost:'.$this->cacheKey()."\\".(isset($extraAttributevaluesId) ? implode('.',
                $extraAttributevaluesId) : '-');

        return (int) Cache::tags([
            'orderproduct', 'attribute', 'orderproductExtraCost', 'orderproduct_'.$orderproduct->id,
            'orderproduct_'.$orderproduct->id.'_orderproductExtraCost'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($extraAttributevaluesId) {
                $extraCost = 0;
                if (isset($extraAttributevaluesId)) {
                    $extraAttributevalues = $this->attributevalues->whereIn('id', $extraAttributevaluesId);
                } else {
                    $extraAttributevalues = $this->attributevalues;
                }
                foreach ($extraAttributevalues as $attributevalue) {
                    $extraCost += $attributevalue->pivot->extraCost;
                }

                return (int) $extraCost;
            });
    }

    /**
     * Get orderproduct's total bon discount
     *
     * @return mixed
     */
    public function getTotalBonDiscountPercentage()
    {
        $totalBonDiscountValue = $this->getTotalBonDiscountDecimalValue();

        return min($totalBonDiscountValue / 100, 1);
    }

    /**
     * Obtains orderproduct's total bon discount decimal value
     *
     * @return int
     */
    public function getTotalBonDiscountDecimalValue(): int
    {
        $totalBonNumber = 0;
        foreach ($this->userbons as $userbon) {
            $totalBonNumber += $userbon->pivot->discount * $userbon->pivot->usageNumber;
        }

        return $totalBonNumber;
    }

    public function isNormalType()
    {
        if ($this->orderproducttype_id == config('constants.ORDER_PRODUCT_TYPE_DEFAULT') || !isset($this->orderproductstatus_id)) {
            return true;
        }

        return false;
    }

    public function fillCostValues($costArray, Coupon $coupon = null)
    {
        if (isset($costArray['cost'])) {
            $this->cost = $costArray['cost'];
        } else {
            $this->cost = null;
        }

        if ($this->isGiftType()) {
            $this->discountPercentage = 100;
            $this->discountAmount = 0;
        } else {
            if (isset($costArray['productDiscount'])) {
                ($coupon && $coupon->is_strict) ? $this->discountPercentage = 0 : $this->discountPercentage = $costArray['productDiscount'];
            }
            if (isset($costArray['productDiscountAmount'])) {
                ($coupon && $coupon->is_strict) ? $this->discountAmount = 0 : $this->discountAmount = $costArray['productDiscountAmount'];
            }
        }
    }

    public function isGiftType()
    {
        if ($this->orderproducttype_id == config('constants.ORDER_PRODUCT_GIFT')) {
            return true;
        }

        return false;
    }

    /**
     * @return BelongsToMany|Orderproduct|Collection
     */
    public function parents()
    {
        return $this->belongsToMany(Orderproduct::class, 'orderproduct_orderproduct', 'op2_id',
            'op1_id')
            ->withPivot('relationtype_id')
            ->join('orderproductinterrelations', 'relationtype_id',
                'orderproductinterrelations.id')
            ->where('relationtype_id', config('constants.ORDER_PRODUCT_INTERRELATION_PARENT_CHILD'));
    }

    /**
     * @param $value
     *
     * @return float|int
     */
    public function getDiscountPercentageAttribute($value)
    {
        return $value / 100;
    }

    public function getPurchasedCouponCodeAttribute()
    {
        $purchasedCoupon = $this->purchasedCoupons->first();
        if (isset($purchasedCoupon)) {
            return $purchasedCoupon->code;
        }

        return null;
    }

    /**
     * Sets orderproduct including in coupon
     *
     */
    public function includeInCoupon(): void
    {
        $this->includedInCoupon = 1;
        $this->update();
    }

    /**
     * Sets orderproduct excluding from coupon
     *
     */
    public function excludeFromCoupon(): void
    {
        $this->includedInCoupon = 0;
        $this->update();
    }

    /**
     * Determines whether orderproduct is available to purchase or not
     *
     * @return bool
     */
    public function isPurchasable(): bool
    {
        return $this->product->isEnableToPurchase();
    }

    /**
     * Updates orderproduct's attribute values
     *
     */
    public function renewAttributeValue(): void
    {
        $extraAttributes = $this->attributevalues;
        $myParent = $this->product->grandParent;

        if (!isset($myParent)) {
            return;
        }
        foreach ($extraAttributes as $extraAttribute) {
            $productAttributevalue = $myParent->attributevalues->where('id', $extraAttribute->id)
                ->first();

            if (!isset($productAttributevalue)) {
                $this->attributevalues()
                    ->detach($productAttributevalue);
            } else {
                $newExtraCost = $productAttributevalue->pivot->extraCost;
                $this->attributevalues()
                    ->updateExistingPivot($extraAttribute->id, ['extraCost' => $newExtraCost]);
            }
        }
    }

    /**
     * @return BelongsToMany|Attributevalue|Collection
     */
    public function attributevalues()
    {
        return $this->belongsToMany(Attributevalue::class, 'attributevalue_orderproduct', 'orderproduct_id', 'value_id')
            ->withPivot('extraCost');
    }

    public function renewUserBons()
    {
        $userbons = $this->userbons;
        if (!$userbons->isNotEmpty()) {
            return;
        }
        $bonName = config('constants.BON1');

        $bons = $this->product->getTotalBons($bonName);

        if ($bons->isEmpty()) {
            foreach ($userbons as $userBon) {
                $this->userbons()
                    ->detach($userBon);

                $userBon->usedNumber = 0;
                $userBon->userbonstatus_id = config('constants.USERBON_STATUS_ACTIVE');
                $userBon->update();
            }
        } else {
            $bon = $bons->first();
            foreach ($userbons as $userbon) {
                $newDiscount = $bon->pivot->discount;
                $this->userbons()
                    ->updateExistingPivot($userbon->id, ['discount' => $newDiscount]);
            }
        }
    }

    /**
     * @return BelongsToMany
     */
    public function userbons()
    {
        return $this->belongsToMany(Userbon::class)
            ->withPivot('usageNumber', 'discount');
    }

    public function getAttachedBonsNumberAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:attachedBonsNumber:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'bon', 'orderproduct_'.$orderproduct->id, 'orderproduct_'.$orderproduct->id.'_bon'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($orderproduct) {
                return $orderproduct->userbons->sum('usedNumber');
            });
    }

    /**
     * @param       $userBons
     * @param  Bon  $bon
     */
    public function applyBons($userBons, Bon $bon): void
    {
        foreach ($userBons as $userBon) {
            $remainBonNumber = $userBon->void();
            $this->userbons()
                ->attach($userBon->id, [
                    'usageNumber' => $remainBonNumber,
                    'discount' => $bon->pivot->discount,
                ]);
        }
        Cache::tags([
            'user_'.$userBons->first()->user_id.'_totalBonNumber',
            'user_'.$userBons->first()->user_id.'_validBons',
            'user_'.$userBons->first()->user_id.'_hasBon',
            'orderproduct_'.$this->id.'_bon',
            'orderproduct_'.$this->id.'_cost',
            'order_'.$this->id.'_cost',
        ])->flush();
    }

    public function getProductAttribute()
    {
        $orderproduct = $this;
        $product = $orderproduct->product()->first();
        if ($product) {
            $key = 'orderproduct:product'.$orderproduct->cacheKey();
            return Cache::tags([
                'orderproduct', 'product', 'orderproduct_'.$orderproduct->id,
                'orderproduct_'.$orderproduct->id.'_product'
            ])
                ->remember($key, config('constants.CACHE_60'), function () use ($product) {
                    return optional($product)->append([
                        'url',
                        'apiUrl',
                        'photo',
                        'attributes',
                        'photo',
                    ])->setVisible([
                        'id',
                        'name',
                        'url',
                        'apiUrl',
                        'photo',
                        'attributes',
                    ]);
                });
        }

    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class); //->with('parents')
    }

    public function scopeFilterProducts($query, array $productIds, array $columns = ['*'])
    {
        return $query->whereIn('product_id', $productIds)->select($columns);
    }

    public function getGrandProductAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:grandProduct:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'product', 'grandProduct', 'orderproduct_'.$orderproduct->id,
            'orderproduct_'.$orderproduct->id.'_grandProduct'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($orderproduct) {
                $grand = optional(optional($this->product)->grand)->append([
                    'photo',
                    'url',
                    'apiUrl',
                    'attributes',
                ]);

                if (!isset($grand)) {
                    return null;
                }

                return $grand->setVisible([
                    'id',
                    'name',
                    'photo',
                    'url',
                    'apiUrl',
                    'attributes',
                ]);
            });
    }

    public function getGrandIdAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:grandProductId:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'product', 'grandProduct', 'orderproduct_'.$orderproduct->id,
            'orderproduct_'.$orderproduct->id.'_grandProduct'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($orderproduct) {
                return optional($orderproduct->product)->grand_id;
            });
    }

    public function getPriceAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:cost:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'orderproductCost', 'cost', 'orderproduct_'.$orderproduct->id,
            'orderproduct_'.$orderproduct->id.'_cost'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($orderproduct) {
                return $this->obtainOrderproductCost(false);
            });
    }

    /**
     * Obtain order total cost
     *
     * @param  boolean  $calculateCost
     *
     * @return array
     */
    public function obtainOrderproductCost($calculateCost = true)
    {
        $priceInfo = $this->calculatePayableCost($calculateCost);

        return [
            'discountDetail' => [
                'productDiscount' => $priceInfo['productDiscount'],
                'bonDiscount' => $priceInfo['bonDiscount'],
                'productDiscountAmount' => $priceInfo['productDiscountAmount'],
            ],
            //////////////////////////
            'extraCost' => $priceInfo['extraCost'],
            'base' => $priceInfo['cost'],
            'discount' => $priceInfo['discount'],
            'final' => $priceInfo['customerCost'],
//          'totalPrice'     => $priceInfo['totalCost'],
        ];
    }

    private function calculatePayableCost($calculateCost = true)
    {
        $alaaCashierFacade = new OrderproductCheckout($this, $calculateCost);
        $priceInfo = $alaaCashierFacade->checkout();
        $calculatedOrderproducts = $priceInfo['orderproductsInfo']['calculatedOrderproducts'];
        /** @var OrderproductCollection $calculatedOrderproducts */
        return $calculatedOrderproducts->getNewPriceForItem($calculatedOrderproducts->first());
    }

    public function getBonsAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:bons:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'bon', 'cost', 'orderproduct_'.$orderproduct->id, 'orderproduct_'.$orderproduct->id.'_bon'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($orderproduct) {
                return $this->userbons()->get();
            });
    }

    public function getAttributevaluesAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:attributevalues:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'attribute', 'attributevalues', 'orderproduct_'.$orderproduct->id,
            'orderproduct_'.$orderproduct->id.'_attributevalues'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($orderproduct) {
                return $orderproduct->attributevalues()->get();
            });
    }

    public function getPhotoAttribute()
    {
        $orderproduct = $this;
        $key = 'orderproduct:photo:'.$orderproduct->cacheKey();

        return Cache::tags([
            'orderproduct', 'photo', 'orderproduct_'.$orderproduct->id, 'orderproduct_'.$orderproduct->id.'_photo'
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($orderproduct) {
                $product = $this->product;
                $grand = $product?->grand;

                if (isset($grand)) {
                    return $grand?->photo;
                }

                return $product?->photo;
            });
    }

    public function getSharedCostOfTransaction()
    {
        $myOrder = $this->order;
        $totalPaidCost = $myOrder->none_wallet_successful_transactions->sum('cost');

        if ($this->isDonate()) {
            return $this->cost; // sahm man az in sefaresh kol costam hast
        }

        if (isset($this->tmp_share_order)) {
            $shareOfOrder = $this->tmp_share_order;
        } else {
            $shareOfOrder = $this->setShareCost();
        }

        $totalPaidCost = max($totalPaidCost - $myOrder->getDonateSum(), 0);
        return $shareOfOrder * $totalPaidCost;
    }

    public function isDonate(): bool
    {
        return in_array($this->product_id, Product::DONATE_PRODUCT_ARRAY);
    }

    /**
     * @param $finalPrice
     * @param $donateOrderproductSum
     *
     * @return float|int
     */
    public function setShareCost()
    {
        if (isset($this->tmp_final_cost)) {
            $finalPrice = $this->tmp_final_cost;
        } else {
            [$finalPrice, $extraCost] = $this->setTmpFinalCost();
        }


        if ($this->isDonate()) {
            $sumOfDonate = $this->order->getDonateSum();
            $shareOfOrder = (double) $finalPrice / $sumOfDonate;
            OrderproductRepo::refreshOrderproductTmpShare($this, $shareOfOrder);
            return $shareOfOrder;
        }
        if ($this->isGiftType() || $this->orderproducttype_id != 1) {
            $shareOfOrder = 0;
            OrderproductRepo::refreshOrderproductTmpShare($this, $shareOfOrder);
            return $shareOfOrder;
        }
        $total = DB::table('orderproducts')
            ->whereNotIn('product_id', Product::DONATE_PRODUCT_ARRAY)
            ->where('order_id', $this->order_id)
            ->whereNull('deleted_at')
            ->whereIn('orderproducttype_id', [1])
            ->select(DB::raw('SUM(tmp_final_cost) as t_total'))
            ->groupBy('order_id')
            ->get();
        $total = $total->first()?->t_total;

        if ($total == 0) {
            $shareOfOrder = 0;
            OrderproductRepo::refreshOrderproductTmpShare($this, $shareOfOrder);
            return $shareOfOrder;
        }
        $shareOfOrder = (double) $finalPrice / $total;
        OrderproductRepo::refreshOrderproductTmpShare($this, $shareOfOrder);
        return $shareOfOrder;
    }

    /**
     * @return array
     */
    public function setTmpFinalCost(): array
    {
        $price = $this->obtainOrderproductCost(false);
        $finalPrice = $price['final'];
        $extraCost = $price['extraCost'];

        OrderproductRepo::refreshOrderproductTmpPrice($this, $finalPrice, $extraCost);

        return [$finalPrice, $extraCost];
    }

    public function affectCouponOnPrice($finalPrice)
    {
        if (!$this->includedInCoupon) {

            return $finalPrice;
        }
        $myOrder = $this->order;
        $orderCouponDiscount = $myOrder->coupon_discount_type;
        if ($orderCouponDiscount !== false) {
            $couponDiscount = $orderCouponDiscount['discount'];
            if ($orderCouponDiscount['typeHint'] == 'percentage') {
                $finalPrice = ($finalPrice * (1 - ($couponDiscount / 100)));
            }
        }


        return $finalPrice;
    }

    public function isRaheAbrisham()
    {
        return in_array($this->product_id, array_keys(Product::ALL_ABRISHAM_PRODUCTS));
    }

    public function reactiveBons()
    {
        try {
            $orderproduct_userbons = $this->userbons;
            foreach ($orderproduct_userbons as $orderproduct_userbon) {
                $orderproduct_userbon->usedNumber =
                    $orderproduct_userbon->usedNumber - $orderproduct_userbon->pivot->usageNumber;
                $orderproduct_userbon->userbonstatus_id = config('constants.USERBON_STATUS_ACTIVE');
                if ($orderproduct_userbon->usedNumber >= 0) {
                    $orderproduct_userbon->update();
                }
            }
        } catch (Exception $exception) {
            throw new Exception('reactive bons face problem', Response::HTTP_SERVICE_UNAVAILABLE);
        }

    }

    public function updateOrderCost()
    {
        $orderCost = $this->order->obtainOrderCost(true, false);
        $this->order->cost = $orderCost['rawCostWithDiscount'];
        $this->order->costwithoutcoupon = $orderCost['rawCostWithoutDiscount'];
        $this->order->updateWithoutTimestamp();
    }

    public function hasPaidForContent(Content $content): bool
    {
        if ($this->inForbiddenTypes()) {
            return false;
        }

        if ($this->inWatchableTypes()) {
            return true;
        }

        if ($this->noPayment()) {
            return false;
        }

        $set = $content->set;
//        if($this->checkBypassedSets($set))
//        {
//            return true;
//        }

        return $this->checkSetPaymentStatus($content);
    }

    public function inForbiddenTypes(): bool
    {
        return in_array($this->orderproducttype_id, [
            config('constants.ORDER_PRODUCT_LOCKED'),
            config('constants.ORDER_PRODUCT_EXCHANGE'),
        ]);
    }

    public function inWatchableTypes(): bool
    {
        return in_array($this->orderproducttype_id, [
            config('constants.ORDER_PRODUCT_GIFT'),
        ]);
    }

    public function noPayment(): bool
    {
        return $this->paidPercent == 0;
    }

    public function checkSetPaymentStatus(Content $content): bool
    {
        $installmentPaidRatio = ($this->paidPercent) / 100;
        $sets = $this->product->sets()->active()
            ->orderBy('pivot_order')
            ->with([
                'contents' => function ($query) {
                    return $query->where('isFree', 0)->where('enable', 1)->orderBy('order');
                }
            ])->get();

        $contents = $sets->pluck('contents')->flatten();
        $available_content_count = ceil($contents->count() * $installmentPaidRatio);
        $available_content = $contents->take($available_content_count);
        $has_access_content = $available_content->where('id', $content->id)->first();

        if ($has_access_content) {
            return true;
        }
        return false;

    }

    public function checkBypassedSets(Contentset $set): bool
    {
        return $this->setIsNotInstallmentlly($set);
    }

    public function setIsNotInstallmentlly(Contentset $set): bool
    {
        // some sets as Pileh should not be included in installment
        // if content belongs to such sets => user can see that
        return $this->product
            ?->sets()
            ?->where('id', $set->id)
            ?->get()
            ?->where('productSet.isInstallmentally', 0)
            ?->isNotEmpty();
    }

    public function setFinancialCategory()
    {
        if (isset($this->financial_category_id)) {
            return;
        }


        $product = $this->product;
        if (is_null($product)) {
            $product = $this->product()->withTrashed()->first();
        }

        if (!isset($product)) {
            return;
        }

        $this->update(['financial_category_id' => $product->financial_category_id]);
    }
}
