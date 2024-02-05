<?php

namespace App\Models;

use App\Classes\Checkout\Alaa\OrderCheckout;
use App\Classes\Checkout\Alaa\ReObtainOrderFromRecords;
use App\Collection\OrderCollections;
use App\Collection\OrderproductCollection;
use App\Collection\ProductCollection;
use App\Collection\TransactionCollection;
use App\Repositories\OrderproductRepo;
use App\Repositories\ProductRepository;
use App\Traits\DateTrait;
use App\Traits\GiveGift\GiveGift;
use App\Traits\Helper;
use App\Traits\InInstalmentsTrait;
use App\Traits\logger;
use App\Traits\ProductCommon;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @property Repository|\Illuminate\Contracts\Foundation\Application|Application|int|mixed|null $orderstatus_id
 */
class Order extends BaseModel implements GiveGift
{
    use HasFactory;

    public const ORDER_STATUS_OPEN = 1;

    public const ORDER_STATUS_OPEN_BY_ADMIN = 4;

    public const ORDER_STATUS_OPEN_DONATE = 8;

    public const ORDER_STATUS_OPEN_3a = 11;

    public const OPEN_ORDER_STATUSES = [
        self::ORDER_STATUS_OPEN,
        self::ORDER_STATUS_OPEN_BY_ADMIN,
        self::ORDER_STATUS_OPEN_DONATE,
        self::ORDER_STATUS_OPEN_3a,
    ];

    public const VALID_DIFF_TOTAL_PAID_COST_AND_ORDER_TOTAL_COST = 5;

    /*
    |--------------------------------------------------------------------------
    | Traits methods
    |--------------------------------------------------------------------------
    */
    use DateTrait;
    use Helper;
    use InInstalmentsTrait;
    use logger;
    use ProductCommon;

    public const LOG_ATTRIBUTES = [
        'orderstatus_id',
        'paymentstatus_id',
        'coupon_id',
        'couponDiscount',
        'cost',
        'costwithoutcoupon',
        'discount',
        'customerDescription',
        'completed_at',
        'user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Properties methods
    |--------------------------------------------------------------------------
    */
    public Carbon|false $completed_at;

    protected $table = 'orders';

    protected $cascadeDeletes = [
        'orderproducts',
        'transactions',
        'files',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        //        'insertor_id',
        'orderstatus_id',
        'paymentstatus_id',
        'coupon_id',
        'referralCode_id',
        'referralCodeDiscount',
        'couponDiscount',
        'couponDiscountAmount',
        'cost',
        'costwithoutcoupon',
        'discount',
        'customerDescription',
        'customerExtraInfo',
        'checkOutDateTime',
        'completed_at',
        'automatic_donation',
        'isInInstalment',
        'seller',
    ];

    protected $appends = [
        'price',
        'orderstatus',
        'paymentstatus',
        'orderproducts',
        'couponInfo',
        'paidPrice',
        'refundPrice',
        'successfulTransactions',
        'pendingTransactions',
        'unpaidTransactions',
        'orderPostingInfo',
        'debt',
        'usedBonSum',
        'addedBonSum',
        'user',
        'jalaliCreatedAt',
        'jalaliUpdatedAt',
        'jalaliCompletedAt',
        'postingInfo',
        'managerComment',
    ];

    protected $hidden = [
        'id',
        'couponDiscount',
        'coupon',
        'orderstatus_id',
        'paymentstatus_id',
        'checkOutDateTime',
        'couponDiscountAmount',
        'coupon_id',
        'referralCode_id',
        'referralCodeDiscount',
        'cost',
        'costwithoutcoupon',
        'normalOrderproducts',
        'user_id',
        'updated_at',
        'deleted_at',
    ];

    protected $with = ['coupon'];

    /**
     * @var array|mixed
     */
    protected $Orderproducts_cache;

    protected $cachedMethods = [
        'getOrderproductsAttribute',
    ];

    public static function getOpenOrderStatus(): array
    {
        return [
            config('constants.ORDER_STATUS_OPEN'), config('constants.ORDER_STATUS_OPEN_3A'),
            config('constants.ORDER_STATUS_OPEN_BY_ADMIN'), config('constants.ORDER_STATUS_OPEN_DONATE'),
        ];
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new OrderCollections($models);
    }

    public function CompletedAt_Jalali()
    {
        /**
         * Unnecessary variable
         */ /*$explodedDateTime = explode(" ", $this->completed_at);*/
        //        $explodedTime = $explodedDateTime[1] ;
        return $this->convertDate($this->completed_at, 'toJalali');
    }

    public function onlinetransactions()
    {
        return $this->hasMany(Transaction::class)
            ->where('paymentmethod_id', Paymentmethod::ONLINE_ID);
    }

    public function archivedSuccessfulTransactions()
    {
        return $this->hasMany(Transaction::class)
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_ARCHIVED_SUCCESSFUL'));
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /*
     * TODO: In this model, there are several methods that have a hasMany relationship with the Transaction model.
     *  While it is more correct to use relationship "transactions" instead of all of them. Of course, I think they
     *  should be a Scope and not a Relationship.
     */

    public function files()
    {
        return $this->hasMany(Orderfile::class);
    }

    public function normalOrderproducts()
    {
        return $this->hasMany(Orderproduct::class)
            ->where(function ($q) {
                /** @var QueryBuilder $q */
                $q->Where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'));
            });
    }

    public function referralCode()
    {
        return $this->belongsTo(ReferralCode::class, 'referralCode_id');
    }

    public function billings()
    {
        return $this->hasMany(Billing::class, 'o_id');
    }

    public function giftOrderproducts()
    {
        return $this->orderproducts(config('constants.ORDER_PRODUCT_GIFT'));
    }

    /**
     * @param  null  $type
     * @param  array  $filters
     * @return HasMany|Orderproduct
     */
    public function orderproducts($type = null, $filters = [])
    {
        if (isset($type)) {
            if ($type == config('constants.ORDER_PRODUCT_TYPE_DEFAULT')) {
                $relation = $this->hasMany(Orderproduct::class)
                    ->where(function ($q) use ($type) {
                        /** @var QueryBuilder $q */
                        $q->where('orderproducttype_id', $type);
                    });
            } else {
                $relation = $this->hasMany(Orderproduct::class)
                    ->where('orderproducttype_id', $type);
            }
        } else {
            $relation = $this->hasMany(Orderproduct::class);
        }

        foreach ($filters as $filter) {
            if (isset($filter['isArray'])) {
                $relation->whereIn($filter['attribute'], $filter['value']);
            } else {
                $relation->where($filter['attribute'], $filter['value']);
            }
        }

        return $relation;
    }

    /**
     * Determines this order's coupon discount type
     * Note: In case it has any coupons returns false
     */
    public function couponDiscountType(): Attribute
    {
        $order = $this;

        return Attribute::make(
            get: function () use ($order) {
                if ($order->couponDiscount == 0 & $order->couponDiscountAmount == 0) {
                    return false;
                }
                if ($order->couponDiscount > 0) {
                    return [
                        'type' => config('constants.DISCOUNT_TYPE_PERCENTAGE'),
                        'typeHint' => 'percentage',
                        'discount' => $order->couponDiscount,
                    ];
                }

                return [
                    'type' => config('constants.DISCOUNT_TYPE_COST'),
                    'typeHint' => 'amount',
                    'discount' => $order->couponDiscountAmount,
                ];
            }
        )->withoutObjectCaching();
    }

    /**
     * Indicated whether order cost has been determined or not
     */
    public function hasCost(): bool
    {
        return isset($this->cost) || isset($this->costwithoutcoupon);
    }

    public function doesBelongToThisUser($user): bool
    {
        return optional($this->user)->id == optional($user)->id;
    }

    /**
     * Calculates the discount amount of totalCost relevant to this order's coupon
     *
     *
     * @return float|int|mixed
     */
    public function obtainCouponDiscount(int $totalCost = 0)
    {
        $couponType = $this->coupon_discount_type;
        if ($couponType === false) {

            return $totalCost;
        }
        if ($couponType['type'] == config('constants.DISCOUNT_TYPE_PERCENTAGE')) {
            $totalCost = ((1 - ($couponType['discount'] / 100)) * $totalCost);
        } else {
            if ($couponType['type'] == config('constants.DISCOUNT_TYPE_COST')) {
                $totalCost = $totalCost - $couponType['discount'];
            }
        }

        return $totalCost;
    }

    public function determineCoupontype()
    {
        if (! $this->hasCoupon()) {
            return false;
        }
        if ($this->couponDiscount > 0) {
            return [
                'type' => config('constants.DISCOUNT_TYPE_PERCENTAGE'),
                'discount' => $this->couponDiscount,
            ];
        }

        return [
            'type' => config('constants.DISCOUNT_TYPE_COST'),
            'discount' => $this->couponDiscountAmount,
        ];
    }

    /**
     * Determines whether order has coupon or not
     *
     * @return bool
     */
    public function hasCoupon()
    {
        return isset($this->coupon->id);
    }

    public function hasReferralCode()
    {
        return isset($this->referralCode->id);
    }

    public function numberOfProducts(): Attribute
    {
        $order = $this;

        return Attribute::make(
            get: function () use ($order) {
                return $order->orderproducts->filterNoneDonation()->count();
            }
        )->withoutObjectCaching();

    }

    /**
     * Gets this order's products
     *
     *
     * @return \Illuminate\Database\Eloquent\Collection|Collection
     */
    public function products(array $orderproductTypes = [])
    {
        $order = $this;
        $key = 'order:products:'.$order->cacheKey();

        return Cache::tags(['order', 'product', 'order_'.$this->id, 'order_'.$this->id.'_products'])
            ->remember($key, config('constants.CACHE_5'), function () use ($orderproductTypes) {
                $result = DB::table('products')
                    ->join('orderproducts', function ($join) use ($orderproductTypes) {
                        if (empty($orderproductTypes)) {
                            $join->on('products.id', '=', 'orderproducts.product_id')
                                ->whereNull('orderproducts.deleted_at');
                        } else {
                            $join->on('products.id', '=', 'orderproducts.product_id')
                                ->whereNull('orderproducts.deleted_at')
                                ->whereIn('orderproducttype_id',
                                    $orderproductTypes);
                        }
                    })
                    ->join('orders', function ($join) {
                        $join->on('orders.id', '=', 'orderproducts.order_id')
                            ->whereNull('orders.deleted_at');
                    })
                    ->select([
                        'products.*',
                    ])
                    ->where('orders.id', '=', $this->getKey())
                    ->whereNull('products.deleted_at')
                    ->distinct()
                    ->get();
                $result = Product::hydrate($result->toArray());

                return $result;

            });
    }

    public function refreshCost()
    {
        $orderCost = $this->obtainOrderCost(true);
        /** @var OrderproductCollection $calculatedOrderproducts */
        $calculatedOrderproducts = $orderCost['calculatedOrderproducts'];
        $calculatedOrderproducts->updateCostValues();

        $this->cost = $orderCost['rawCostWithDiscount'];
        $this->costwithoutcoupon = $orderCost['rawCostWithoutDiscount'];
        //        $this->instalmentCost    = $orderCost["rawInstalmentCost"];
        $this->updateWithoutTimestamp();

        return ['newCost' => $orderCost];
    }

    /**
     * Obtain order total cost
     *
     * @param  bool  $calculateOrderCost
     * @param  bool  $calculateOrderproductCost
     * @param  string  $mode
     * @return array
     */
    public function obtainOrderCost($calculateOrderCost = false, $calculateOrderproductCost = true, $mode = 'DEFAULT')
    {
        if ($calculateOrderCost) {
            $this->load('user', 'user.wallets', 'normalOrderproducts', 'normalOrderproducts.product',
                'normalOrderproducts.product.parents',
                'normalOrderproducts.userbons', 'normalOrderproducts.attributevalues',
                'normalOrderproducts.product.attributevalues', 'coupon');
            $orderproductsToCalculateFromBaseIds = [];
            if ($calculateOrderproductCost) {
                $orderproductsToCalculateFromBaseIds = $this->normalOrderproducts->pluck('id')->toArray();
            }

            $reCheckIncludedOrderproductsInCoupon = false;
            if ($this->hasCoupon()) {
                $reCheckIncludedOrderproductsInCoupon = ($mode == 'REOBTAIN') ? false : true;
            }
            $alaaCashierFacade = new OrderCheckout($this, $orderproductsToCalculateFromBaseIds,
                $reCheckIncludedOrderproductsInCoupon);
        } else {
            $this->load('normalOrderproducts', 'normalOrderproducts.product',
                'normalOrderproducts.product.parents', 'normalOrderproducts.userbons',
                'normalOrderproducts.attributevalues', 'normalOrderproducts.product.attributevalues');
            $alaaCashierFacade = new ReObtainOrderFromRecords($this);
        }

        $priceInfo = $alaaCashierFacade->checkout();

        return [
            'sumOfOrderproductsRawCost' => $priceInfo['totalPriceInfo']['sumOfOrderproductsRawCost'],
            'rawCostWithDiscount' => $priceInfo['totalPriceInfo']['totalRawPriceWhichHasDiscount'],
            'rawCostWithoutDiscount' => $priceInfo['totalPriceInfo']['totalRawPriceWhichDoesntHaveDiscount'],
            //            'rawInstalmentCost'             => $priceInfo['totalPriceInfo']['totalRawInstalmentPrice'],
            'totalCost' => $priceInfo['totalPriceInfo']['finalPrice'],
            'totalCostWithoutOrderDiscount' => $priceInfo['totalPriceInfo']['totalPrice'],
            //            'finalPriceWithInstalment' => $priceInfo['totalPriceInfo']['finalPriceWithInstalment'],
            'payableAmountByWallet' => $priceInfo['totalPriceInfo']['payableAmountByWallet'],
            'calculatedOrderproducts' => $priceInfo['orderproductsInfo']['calculatedOrderproducts'],
        ];
    }

    public function refreshCostWithoutReobtain()
    {
        $orderCost = $this->obtainOrderCost(true, false);

        $this->cost = $orderCost['rawCostWithoutDiscount'];
        $this->costwithoutcoupon = $orderCost['rawCostWithDiscount'];
        $this->updateWithoutTimestamp();

        return ['newCost' => $orderCost];
    }

    /**
     * Gives order bons to user
     *
     * @param  string  $bonName
     * @return array [$totalSuccessfulBons, $totalFailedBons]
     */
    public function giveUserBons($bonName)
    {
        $totalSuccessfulBons = 0;
        $totalFailedBons = 0;
        $user = $this->user;
        if (! isset($user)) {
            return [0, 0];
        }
        $orderproducts = $this->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))->get();
        foreach ($orderproducts as $orderproduct) {
            if ($user->userbons->where('orderproduct_id', $orderproduct->id)
                ->isNotEmpty()) {
                continue;
            }
            /** @var Product $simpleProduct */
            $simpleProduct = $orderproduct->product;
            $bons = $simpleProduct->bons->where('name', $bonName)->where('isEnable', 1);
            if ($bons->isEmpty()) {
                $grandParent = $simpleProduct->grand_parent;
                if (isset($grandParent)) {
                    $bons = $grandParent->bons->where('name', $bonName)->where('isEnable', 1);
                }
            }

            if (! $bons->isNotEmpty()) {
                continue;
            }
            $bon = $bons->first();
            $bonPlus = $bon->pivot->bonPlus;
            if ($bonPlus) {
                $userbon = Userbon::create([
                    'user_id' => $user->id,
                    'bon_id' => $bon->id,
                    'totalNumber' => $bon->pivot->bonPlus,
                    'userbonstatus_id' => config('constants.USERBON_STATUS_ACTIVE'),
                    'orderproduct_id' => $orderproduct->id,
                ]);
                if (isset($userbon)) {
                    $totalSuccessfulBons += $userbon->totalNumber;
                } else {
                    $totalFailedBons += $bon->pivot->bonPlus;
                }
            }

        }

        return [
            $totalSuccessfulBons,
            $totalFailedBons,
        ];
    }

    public function closeWalletPendingTransactions()
    {
        /**
         * for reduce query
         */
        /*$walletTransactions = $this->suspendedTransactions*/
        $walletTransactions =
            $this->suspendedTransactions()->where('paymentmethod_id', config('constants.PAYMENT_METHOD_WALLET'))->get();

        foreach ($walletTransactions as $transaction) {
            /** @var Transaction $transaction */
            $transaction->transactionstatus_id = config('constants.TRANSACTION_STATUS_SUCCESSFUL');
            $transaction->update();
        }
    }

    /**
     * @return HasMany|Transaction
     */
    public function suspendedTransactions()
    {
        return $this->hasMany(Transaction::class)
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUSPENDED'));
    }

    public function checkProductsExistInOrderProducts(ProductCollection $products): ProductCollection
    {
        $notDuplicateProduct = new ProductCollection();
        foreach ($products as $product) {
            $this->checkSubscriptionProducts($product->id);
            if ($this->hasTheseProducts([$product->id])) {
                // can increase amount of product
            } else {
                $notDuplicateProduct->push($product);
            }
        }

        return $notDuplicateProduct;
    }

    private function checkSubscriptionProducts(int $productId)
    {
        $user = $this->user;
        if (! isset($user)) {
            return null;
        }

        if (! in_array($productId,
            [Product::SUBSCRIPTION_1_MONTH, Product::SUBSCRIPTION_3_MONTH, Product::SUBSCRIPTION_12_MONTH])) {
            return null;
        }

        $subscriptionOrderproducts = $this->orderproducts->whereIn('product_id',
            [Product::SUBSCRIPTION_1_MONTH, Product::SUBSCRIPTION_3_MONTH, Product::SUBSCRIPTION_12_MONTH]);
        foreach ($subscriptionOrderproducts as $subscriptionOrderproduct) {
            $subscriptionOrderproduct->delete();
        }

        return null;
    }

    /**
     * Determines if this order has given products
     */
    public function hasTheseProducts(array $products): bool
    {
        return $this->orderproducts->whereIn('product_id', $products)->isNotEmpty();
    }

    public function getDonateCost(): int
    {
        $donateCost = 0;
        $orderProducts = $this->orderproducts->whereIn('product_id', [
            Product::CUSTOM_DONATE_PRODUCT,
            Product::DONATE_PRODUCT_5_HEZAR,
        ]);

        foreach ($orderProducts as $orderProduct) {
            $donateCost += $orderProduct->cost;
        }

        return $donateCost;
    }

    public function closeOrderWithIndebtedStatus()
    {
        $this->close(config('constants.PAYMENT_STATUS_INDEBTED'));
        $this->timestamps = false;
        $this->update();
        $this->timestamps = true;
    }

    /**
     * Closes this order
     *
     * @param  string  $paymentStatus
     * @return void
     */
    public function close($paymentStatus = null, ?int $orderStatus = null)
    {
        if (is_null($orderStatus)) {
            // You can't put config() in method signature
            $orderStatus = config('constants.ORDER_STATUS_CLOSED');
        }

        $this->orderstatus_id = $orderStatus;

        if (isset($paymentStatus)) {
            $this->paymentstatus_id = $paymentStatus;
        }

        $this->completed_at = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now('Asia/Tehran'));
    }

    public function detachUnusedCoupon()
    {
        $usedCoupon = $this->hasProductsThatUseItsCoupon();
        if ($usedCoupon) {
            return null;
        }
        /** if order has not used coupon reverse it    */
        $coupon = $this->coupon;
        if (! isset($coupon)) {
            return null;
        }
        $this->detachCoupon();
        if ($this->updateWithoutTimestamp()) {
            $coupon->decreaseUseNumber();
            $coupon->update();
        }
    }

    /**
     * Determines whether the coupon is usable for this order or not
     */
    public function hasProductsThatUseItsCoupon(): bool
    {
        $flag = true;
        $notIncludedProducts = $this->reviewCouponProducts();
        $orderproductCount = $this->orderproducts->whereType([config('constants.ORDER_PRODUCT_TYPE_DEFAULT')])
            ->count();
        if ($orderproductCount == optional($notIncludedProducts)->count()) {
            $flag = false;
        }

        return $flag;
    }

    public function reviewCouponProducts(): ?Collection
    {
        $orderproducts = $this->orderproducts->whereType([config('constants.ORDER_PRODUCT_TYPE_DEFAULT')]);

        $coupon = $this->coupon;
        $notIncludedProducts = new ProductCollection();
        if (isset($coupon)) {
            /** @var OrderproductCollection $orderproducts */
            foreach ($orderproducts->getPurchasedProducts() as $product) {
                if (! $coupon->hasProduct($product)) {
                    $notIncludedProducts->push($product);
                }
            }
        }

        if ($notIncludedProducts->isNotEmpty()) {
            return $notIncludedProducts;
        }

        return null;
    }

    public function hasProduct($product)
    {
        if (is_array($product)) {
            return $this->orderproducts->whereIn('product_id', $product)->isNotEmpty();
        }

        return $this->orderproducts->where('product_id', $product)->isNotEmpty();
    }

    /**
     * Detaches coupon from this order
     */
    public function detachCoupon(): void
    {
        $this->coupon_id = null;
        $this->couponDiscount = 0;
        $this->couponDiscountAmount = 0;
        $this->orderproducts->each(function ($orderProduct) {
            $orderProduct->update(['includedInCoupon' => 0]);
        });
    }

    /**
     * Detaches referral code from this order
     */
    public function detachReferralCode(): void
    {
        $this->referralCode_id = null;
        $this->referralCodeDiscount = 0;
    }

    /**
     * Detaches coupon from orderproduct
     */
    public function detachCouponFromOrderproduct(): void
    {
        foreach ($this->orderproducts as $orderproduct) {
            $orderproduct->excludeFromCoupon();
        }
    }

    public function attachCoupon(Coupon $coupon): self
    {
        $this->coupon_id = $coupon->id;
        if ($coupon->discounttype_id == config('constants.DISCOUNT_TYPE_COST')) {
            $this->couponDiscount = 0;
            $this->couponDiscountAmount = (int) $coupon->discount;
        } else {
            $this->couponDiscount = $coupon->discount;
            $this->couponDiscountAmount = 0;
        }

        return $this;
    }

    public function attachReferralCode(ReferralCode $referralCode): self
    {
        $this->referralCode_id = $referralCode->id;
        $this->referralCodeDiscount = (int) $referralCode->referralRequest->discount;

        return $this;
    }

    /**
     * @return int $totalWalletRefund
     */
    public function refundWalletTransaction(): int
    {
        $walletTransactions = $this->suspendedTransactions()
            ->walletMethod()
            ->get();

        $totalWalletRefund = 0;
        foreach ($walletTransactions as $transaction) {
            $response = $transaction->depositThisWalletTransaction();
            if ($response['result']) {
                $transaction->delete();
                $totalWalletRefund += $transaction->cost;
            }
        }

        return $totalWalletRefund;
    }

    public function getOrderstatusAttribute()
    {
        $order = $this;
        $key = 'order:orderstatus:'.$order->cacheKey();

        return Cache::tags(['order', 'orderstatus', 'order_'.$order->id, 'order_'.$order->id.'_orderstatus'])
            ->remember($key, config('constants.CACHE_10'), function () use ($order) {
                return optional($order->orderstatus()
                    ->first())->setVisible([
                        'name',
                        'displayName',
                        'description',
                    ]);
            });
    }

    public function orderstatus()
    {
        return $this->belongsTo(Orderstatus::class);
    }

    public function getPaymentstatusAttribute()
    {
        $order = $this;
        $key = 'order:paymentstatus:'.$order->cacheKey();

        return Cache::tags(['order', 'paymentstatus', 'order_'.$order->id, 'order_'.$order->id.'_paymentstatus'])
            ->remember($key, config('constants.CACHE_10'), function () use ($order) {
                return optional($order->paymentstatus()
                    ->first())->setVisible([
                        'name',
                        'displayName',
                        'description',
                    ]);
            });
    }

    public function paymentstatus()
    {
        return $this->belongsTo(Paymentstatus::class);
    }

    public function getCouponInfoAttribute(): Attribute
    {
        $order = $this;

        return Attribute::make(
            get: function () use ($order) {
                $key = 'order:coupon:'.$order->cacheKey();

                return Cache::tags([
                    'coupon', 'order', 'order_'.$this->id, 'order_'.$this->id.'_coupon', 'order_'.$this->id.'_couponInfo',
                    'coupon_user_'.$this->user_id,
                ])
                    ->remember($key, config('constants.CACHE_10'), function () use ($order) {
                        $coupon = $order->coupon()
                            ->first();
                        if (! isset($coupon)) {
                            return null;
                        }

                        $coupon->setVisible([
                            'name',
                            'code',
                            //                 'discountType',
                        ]);

                        $discountType = $this->coupon_discount_type;

                        return array_merge($coupon->toArray(), ($discountType === false) ? [] : $discountType);
                    });
            }
        )->withoutObjectCaching();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function getCouponInfo2Attribute()
    {
        // To Do : in line CAST(orders.couponDiscount as UNSIGNED) :
        // I change it from coupons.discount to orders.couponDiscount for bfriday coupon
        // this will be ok unless we use coupons with cost discounttype (discounttype = 2)
        $key = 'order:couponInfo:'.$this->cacheKey();

        return Cache::tags([
            'coupon', 'order', 'order_'.$this->id, 'order_'.$this->id.'_coupon', 'order_'.$this->id.'_couponInfo',
            'coupon_user_'.$this->user_id,
        ])->
        remember($key, config('constants.CACHE_600'), function () {
            $modelKey = $this->getKey();
            $result = DB::query()
                ->fromSub(function ($qqQuery) use ($modelKey) {
                    $qqQuery->fromSub(function ($pQuery) use ($modelKey) {
                        $pQuery->fromSub(function ($query) use ($modelKey) {
                            $query->from('orders')
                                ->join('coupons', function ($join) {
                                    $join->on('orders.coupon_id', '=', 'coupons.id')
                                        ->whereNull('coupons.deleted_at');
                                })
                                ->join('orderproducts', function ($join) {
                                    $join->on('orders.id', '=', 'orderproducts.order_id')
                                        ->whereNull('orderproducts.deleted_at')
                                        ->where('orderproducts.includedInCoupon', '1');
                                })
                                ->join('products', function ($join) {
                                    $join->on('orderproducts.product_id', '=', 'products.id')
                                        ->whereNull('products.deleted_at');
                                })
                                ->select([
                                    'products.id as product_id',
                                    'products.name as product_name',
                                    DB::raw('CAST(products.basePrice * (1-(products.discount/100)) as UNSIGNED) as price_before_coupon'),
                                    DB::raw('CAST(orders.couponDiscount as UNSIGNED) as coupon_discount'),
                                    'coupons.name as coupon_name',
                                    'coupons.code as coupon_code',
                                ])
                                ->where('orders.id', '=', $modelKey);
                        }, 't')
                            ->select([
                                '*',
                                DB::raw('CAST(price_before_coupon * (1-(coupon_discount/100)) as UNSIGNED ) as price_after_coupon'),
                            ]);
                    }, 'tt')
                        ->select([
                            'coupon_name',
                            'coupon_code',
                            'price_before_coupon',
                            'price_after_coupon',
                            DB::raw('JSON_OBJECT(\'product_id\',product_id,
                    \'product_name\',product_name,
                    \'coupon_discount\',coupon_discount,
                    \'price_before_coupon\',price_before_coupon,
                    \'price_after_coupon\',price_after_coupon) as detail'),
                        ]);
                }, 'mm')
                ->select([
                    'coupon_name',
                    'coupon_code',
                    DB::raw('SUM(price_before_coupon) - SUM(price_after_coupon) as total_discount'),
                    DB::raw('COUNT(*) as number_of_products'),
                    DB::raw('JSON_ARRAY(GROUP_CONCAT(detail)) as detail'),
                ])
                ->groupBy('coupon_code');

            $result = $result->get()->toArray();
            if (! (empty($result) && isset($this->coupon_id))) {

                return CouponDetail::hydrate($result);
            }
            $coupon = $this->coupon;
            $result = [
                [
                    'coupon_name' => $coupon->name,
                    'coupon_code' => $coupon->code,
                    'total_discount' => 0,
                    'number_of_products' => 0,
                ],
            ];

            return CouponDetail::hydrate($result);
        });
    }

    public function getWalletSuccessfulTransactionsAttribute()
    {
        return $this->transactions
            ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_WALLET'))
            ->whereIn('transactionstatus_id', [config('constants.TRANSACTION_STATUS_SUCCESSFUL')])
            ->where('cost', '>', 0);
    }

    public function getNoneWalletSuccessfulTransactionsAttribute()
    {
        return $this->transactions
            ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'));
    }

    public function getPriceAttribute()
    {
        return $this->totalCost();
    }

    /**
     * Recalculates order's cost and updates it's cost
     *
     * @return int
     */
    //ToDo : must refresh donate product cost
    public function totalCost()
    {
        return (int) $this->obtainOrderCost()['totalCost'];
    }

    public function applyOrderGifts(Orderproduct $orderProduct, Product $product)
    {
        $giftsOfProduct = $product->getGifts();
        $orderGifts = $this->giftOrderproducts;
        foreach ($giftsOfProduct as $giftItem) {
            if (! $orderGifts->contains($giftItem)) {
                $this->attachGift($giftItem, $orderProduct);
                $this->giftOrderproducts->push($giftItem);
            }
        }
    }

    /** Attaches a gift to the order of this orderproduct
     *
     *
     * @return Orderproduct|null
     */
    public function attachGift(Product $gift, Orderproduct $orderproduct): Orderproduct
    {
        $giftOrderproduct =
            OrderproductRepo::createGiftOrderproduct($this->id, $gift->id, $gift->calculatePayablePrice()['cost']);

        $giftOrderproduct->parents()
            ->attach($orderproduct,
                ['relationtype_id' => config('constants.ORDER_PRODUCT_INTERRELATION_PARENT_CHILD')]);

        return $giftOrderproduct;
    }

    public function getOrderproductsAttribute(): Collection
    {
        if (! is_null($this->Orderproducts_cache)) {
            return $this->Orderproducts_cache;
        }
        $order = $this;
        $key = 'order:orderproducts:'.$order->cacheKey();

        $this->Orderproducts_cache = Cache::tags([
            'order',
            'orderproduct',
            'order_'.$order->id,
            'order_'.$order->id.'_orderproducts',
        ])
            ->remember($key, config('constants.CACHE_5'), function () {
                /** @var OrderproductCollection $orderproducts */
                $orderproducts = $this->orderproducts()
                    ->get();
                if (! $orderproducts->isNotEmpty()) {
                    return $orderproducts;
                }
                $orderproducts->setVisible([
                    'id',
                    'cost',
                    'discountPercentage',
                    'discountAmount',
                    'quantity',
                    'orderproducttype',
                    'product',
                    'grandId',
                    'price',
                    'bons',
                    'attributevalues',
                    'photo',
                    'grandProduct',
                    'purchased_coupon_code',
                ]);

                return $orderproducts;
            });

        return $this->Orderproducts_cache;
    }

    public function getPaidPriceAttribute(): int
    {
        return $this->totalPaidCost() + $this->totalRefund();
    }

    public function totalPaidCost()
    {
        $order = $this;
        $key = 'order:totalPaidCost:'.$order->cacheKey();

        return (int) Cache::tags(['order', 'orderCost', 'cost', 'order_'.$order->id, 'order_'.$order->id.'_cost'])
            ->remember($key, config('constants.CACHE_60'), function () use ($order) {
                $totalPaidCost = 0;
                $successfulTransactions = $order->successfulTransactions;
                if ($successfulTransactions->isNotEmpty()) {
                    $totalPaidCost = $successfulTransactions->where('cost', '>', 0)
                        ->sum('cost');
                }

                return $totalPaidCost;
            });
    }

    public function totalRefund()
    {
        $order = $this;
        $key = 'order:totalRefund:'.$order->cacheKey();

        return (int) Cache::tags(['order', 'orderCost', 'cost', 'order_'.$order->id, 'order_'.$order->id.'_cost'])
            ->remember($key, config('constants.CACHE_60'), function () use ($order) {
                $totalRefund = 0;
                $successfulTransactions = $order->successfulTransactions;
                if ($successfulTransactions->isNotEmpty()) {
                    $totalRefund = $successfulTransactions->where('cost', '<', 0)
                        ->sum('cost');
                }

                return $totalRefund;
            });
    }

    public function getRefundPriceAttribute(): int
    {
        return $this->totalRefund();
    }

    public function getDonatesAttribute(): Collection
    {
        $order = $this;
        $key = 'order:donates:'.$order->cacheKey();

        return Cache::tags([
            'order', 'orderproduct', 'donate', 'order_'.$order->id, 'order_'.$order->id.'_orderproducts',
        ])
            ->remember($key, config('constants.CACHE_10'), function () {
                return $this->orderproducts->whereIn('product_id', [
                    Product::CUSTOM_DONATE_PRODUCT,
                    Product::DONATE_PRODUCT_5_HEZAR,
                ]);
            });
    }

    public function getDonateAmountAttribute(): int
    {
        $donateOrderProducts = $this->donates;

        $donateCost = 0;
        if ($donateOrderProducts->isNotEmpty()) {
            $donateCost = $donateOrderProducts->sum('cost');
        }

        return $donateCost;
    }

    public function getSuccessfulTransactionsAttribute()
    {
        $order = $this;
        $key = 'order:transactions:'.$order->cacheKey();

        return Cache::tags(['order', 'transaction', 'order_'.$order->id, 'order_'.$order->id.'_transactions'])
            ->remember($key, config('constants.CACHE_60'), function () use ($order) {
                /** @var TransactionCollection $successfulTransactions */
                $successfulTransactions = $order->successfulTransactions()
                    ->get();
                $successfulTransactions->setVisible([
                    'cost',
                    'transactionID',
                    'traceNumber',
                    'referenceNumber',
                    'paycheckNumber',
                    'description',
                    'completed_at',
                    'paymentmethod',
                    'transactiongateway',
                    'managerComment',
                    'jalaliCompletedAt',
                ]);

                return $successfulTransactions;
            });
    }

    public function successfulTransactions()
    {
        return $this->hasMany(Transaction::class)
            ->whereIn('transactionstatus_id', [
                config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                config('constants.TRANSACTION_STATUS_SUSPENDED'),
            ]);
    }

    public function getPendingTransactionsAttribute()
    {
        $order = $this;
        $key = 'order:pendingtransactions:'.$order->cacheKey();

        return Cache::tags([
            'order', 'transaction', 'pendingTransaction', 'order_'.$order->id,
            'order_'.$order->id.'_pendingtransactions', 'order_'.$order->id.'_transactions',
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($order) {
                /** @var TransactionCollection $pendingTransaction */
                $pendingTransaction = $order->pendingTransactions()
                    ->get();
                $pendingTransaction->setVisible([
                    'cost',
                    'transactionID',
                    'traceNumber',
                    'referenceNumber',
                    'paycheckNumber',
                    'description',
                    'completed_at',
                    'paymentmethod',
                    'transactiongateway',
                    'managerComment',
                    'jalaliCompletedAt',
                ]);

                return $pendingTransaction;
            });
    }

    public function pendingTransactions()
    {
        return $this->hasMany(Transaction::class)
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_PENDING'));
    }

    public function getUnpaidTransactionsAttribute()
    {
        $order = $this;
        $key = 'order:unpaidtransactions:'.$order->cacheKey();

        return Cache::tags([
            'order', 'transaction', 'unpaidtransactions', 'order_'.$order->id,
            'order_'.$order->id.'_unpaidtransactions', 'order_'.$order->id.'_transactions',
        ])
            ->remember($key, config('constants.CACHE_60'), function () use ($order) {
                /** @var TransactionCollection $unpaidTransaction */
                $unpaidTransaction = $order->unpaidTransactions()
                    ->get();
                $unpaidTransaction->setVisible([
                    'cost',
                    'transactionID',
                    'traceNumber',
                    'referenceNumber',
                    'paycheckNumber',
                    'description',
                    'completed_at',
                    'paymentmethod',
                    'transactiongateway',
                    'managerComment',
                    'jalaliCompletedAt',
                    'jalaliDeadlineAt',
                ]);

                return $unpaidTransaction;
            });
    }

    public function unpaidTransactions()
    {
        return $this->hasMany(Transaction::class)
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_UNPAID'));
    }

    public function getOrderPostingInfoAttribute()
    {
        $order = $this;
        $key = 'order:postInfo:'.$order->cacheKey();

        return Cache::tags(['order', 'postingInfo', 'order_'.$order->id, 'order_'.$order->id.'_postingInfo'])
            ->remember($key, config('constants.CACHE_600'), function () use ($order) {
                return $order->orderpostinginfos()
                    ->get();
            });
    }

    public function orderpostinginfos()
    {
        return $this->hasMany(Orderpostinginfo::class);
    }

    public function getDebtAttribute()
    {
        return $this->debt();
    }

    public function debt()
    {
        $order = $this;
        $key = 'order:debt:'.$order->cacheKey();

        return (int) Cache::tags([
            'order', 'transaction', 'orderDebt', 'order_'.$order->id, 'order_'.$order->id.'_orderDebt',
            'order_'.$order->id.'_transactions',
        ])
            ->remember($key, config('constants.CACHE_60'), function () {
                if ($this->orderstatus_id == config('constants.ORDER_STATUS_REFUNDED')) {
                    return -($this->totalPaidCost() + $this->totalRefund());
                }

                $cost = $this->obtainOrderCost()['totalCost'];

                return $cost - ($this->totalPaidCost() + $this->totalRefund());
            });
    }

    public function getUsedBonSumAttribute()
    {
        return $this->usedBonSum();
    }

    public function usedBonSum()
    {
        $order = $this;
        $key = 'order:usedBonSum:'.$order->cacheKey();

        return (int) Cache::tags([
            'order', 'bon', 'order_'.$order->id, 'order_'.$order->id.'_usedBon', 'order_'.$order->id.'_bon',
        ])
            ->remember($key, config('constants.CACHE_600'), function () {
                $bonSum = 0;
                if (isset($this->orderproducts)) {
                    foreach ($this->orderproducts as $orderproduct) {
                        $bonSum += $orderproduct->userbons->sum('pivot.usageNumber');
                    }
                }

                return $bonSum;
            });
    }

    public function getAddedBonSumAttribute()
    {
        return $this->addedBonSum();
    }

    public function addedBonSum($intendedUser = null)
    {
        $order = $this;
        $key = 'order:addedBonSum:'.$order->cacheKey();

        return Cache::tags([
            'order', 'bon', 'order_'.$order->id, 'order_'.$order->id.'_addedBon', 'order_'.$order->id.'_bon',
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($intendedUser) {
                /** @var User $user */
                if (isset($intendedUser)) {
                    $user = $intendedUser;
                } else {
                    $user = $this->user;
                }

                $bonSum = 0;
                foreach ($this->orderproducts as $orderproduct) {
                    /** @var Collection $userbons */
                    $userbons = $user->userbons->where('orderproduct_id', $orderproduct->id);
                    if ($userbons->isNotEmpty()) {
                        $bonSum += $userbons->sum('totalNumber');
                    }
                }

                return $bonSum;
            });
    }

    public function getUserAttribute()
    {

        $order = $this;
        $key = 'order:user:'.$order->cacheKey();

        return Cache::tags(['order', 'user', 'order_'.$order->id, 'order_'.$order->id.'_user'])
            ->remember($key, config('constants.CACHE_600'), function () use ($order) {
                $visibleColumns = [
                    'id',
                    'firstName',
                    'lastName',
                    'nationalCode',
                    'province',
                    'city',
                    'address',
                    'postalCode',
                    'school',
                    'info',
                    'userstatus',
                ];

                if (hasAuthenticatedUserPermission(config('constants.SHOW_USER_MOBILE'))) {
                    $visibleColumns = array_merge($visibleColumns, ['mobile']);
                }

                if (hasAuthenticatedUserPermission(config('constants.SHOW_USER_EMAIL'))) {
                    $visibleColumns = array_merge($visibleColumns, ['email']);
                }

                return $order->user()
                    ->first()
                    ->setVisible($visibleColumns);
            });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getJalaliUpdatedAtAttribute()
    {
        $order = $this;
        $key = 'order:jalaliUpdatedAt:'.$order->cacheKey();

        return Cache::tags(['order', 'jalaliUpdatedAt', 'order_'.$order->id, 'order_'.$order->id.'_jalaliUpdatedAt'])
            ->remember($key, config('constants.CACHE_600'), function () use ($order) {
                if (isset($order->updated_at) && hasAuthenticatedUserPermission(config('constants.SHOW_ORDER_ACCESS'))) {
                    return $this->convertDate($order->updated_at, 'toJalali');
                }

                return null;
            });

    }

    //    public function inserter(): BelongsTo
    //    {
    //        return $this->belongsTo(\App\Models\User::class, 'insertor_id');
    //    }

    public function getJalaliCreatedAtAttribute()
    {
        $order = $this;
        $key = 'order:jalaliCreatedAt:'.$order->cacheKey();

        return Cache::tags(['order', 'jalaliCreatedAt', 'order_'.$order->id, 'order_'.$order->id.'_jalaliCreatedAt'])
            ->remember($key, config('constants.CACHE_600'), function () use ($order) {
                if (isset($order->created_at) && hasAuthenticatedUserPermission(config('constants.SHOW_ORDER_ACCESS'))) {
                    return $this->convertDate($order->created_at, 'toJalali');
                }

                return null;
            });

    }

    public function getJalaliCompletedAtAttribute()
    {
        $order = $this;
        $key = 'order:jalaliCompletedAt:'.$order->cacheKey();

        return Cache::tags([
            'order', 'jalaliCompletedAt', 'order_'.$order->id, 'order_'.$order->id.'_jalaliCompletedAt',
        ])
            ->remember($key, config('constants.CACHE_600'), function () use ($order) {
                if (isset($order->completed_at) && hasAuthenticatedUserPermission(config('constants.SHOW_ORDER_ACCESS'))) {
                    return $this->CompletedAt_Jalali_WithTime();
                }

                return null;
            });
    }

    public function getPostingInfoAttribute()
    {

        $order = $this;
        $key = 'order:postingInfo:'.$order->cacheKey();

        return Cache::tags(['order'])
            ->remember($key, config('constants.CACHE_60'), function () use ($order) {
                return $order->orderpostinginfos()
                    ->get();
            });

    }

    public function getManagerCommentAttribute()
    {
        $order = $this;
        $key = 'order:managerComment:'.$order->cacheKey();

        return Cache::tags(['order', 'managerComment', 'order_'.$order->id, 'order_'.$order->id.'_managerComment'])
            ->remember($key, config('constants.CACHE_600'), function () use ($order) {
                if (hasAuthenticatedUserPermission('constants.SHOW_ORDER_ACCESS')) {
                    return $order->ordermanagercomments()
                        ->get();
                }

                return null;
            });

    }

    public function ordermanagercomments()
    {
        return $this->hasMany(Ordermanagercomment::class);
    }

    public function getRemoveLinkAttribute()
    {
        if (hasAuthenticatedUserPermission(config('constants.REMOVE_ORDER_ACCESS'))) {
            return action('Web\OrderController@destroy', $this->id);
        }

        return null;
    }

    public function getPurchasedOrderproductsAttribute()
    {
        return $this->normalOrderproducts->whereNotIn('product_id', ProductRepository::getUnPurchasableProducts());
    }

    public function getPurchasedOrderproductsCountAttribute()
    {
        return $this->purchased_orderproducts->count();
    }

    /**
     * @param  Order  $myOrder
     */
    public function getDonateSum(): int
    {
        $key = 'getDonateSum:'.$this->cacheKey();

        return Cache::remember($key, config('constants.CACHE_5'), function () {
            return $this->orderproducts->whereIn('product_id', ProductRepository::getDonateProducts())->sum('cost');
        });
    }

    public function hasDonate(): bool
    {
        return $this->hasTheseProducts([
            Product::CUSTOM_DONATE_PRODUCT,
            Product::DONATE_PRODUCT_5_HEZAR,
        ]);
    }

    public function couponProduct(): ?Orderproduct
    {
        return $this->orderproducts->where('product_id', Product::COUPON_PRODUCT)->first();
    }

    public function getSubscriptionOrderproduct(): Collection
    {
        return $this->orderproducts()->whereHas('product', function ($q) {
            $q->where('producttype_id', config('constants.PRODUCT_TYPE_SUBSCRIPTION'));
        })->get();
    }

    public function raheAbrisham99RiyaziPack()
    {
        return $this->orderproducts->where('product_id', Product::RAHE_ABRISHAM99_PACK_RIYAZI)->isNotEmpty();
    }

    public function raheAbrisham99TajrobiPack()
    {
        return $this->orderproducts->where('product_id', Product::RAHE_ABRISHAM99_PACK_TAJROBI)->isNotEmpty();
    }

    public function abrishamPro()
    {
        return $this->orderproducts->whereIn('product_id', array_keys(Product::ALL_ABRISHAM_PRO_PRODUCTS));
    }

    public function abrishamProTabdil()
    {
        return $this->orderproducts->whereIn('product_id', array_keys(Product::ALL_ABRISHAM_PRO_TABDIL));
    }

    public function tooreRaheAbrisham1400()
    {
        return $this->orderproducts->whereIn('product_id',
            [Product::TOOR_ABRISHAM_1400_TAJROBRI, Product::TOOR_ABSRIHAM_1400_RIYAZI])->isNotEmpty();
    }

    public function tooreRaheAbrishamRiyazi1400()
    {
        return $this->orderproducts->where('product_id', Product::TOOR_ABSRIHAM_1400_RIYAZI)->isNotEmpty();
    }

    public function taftan1401RiyaziPack()
    {
        return $this->orderproducts->where('product_id', Product::TAFTAN1401_RIYAZI_PACKAGE)->isNotEmpty();
    }

    public function taftan1401TajrobiPack()
    {
        return $this->orderproducts->where('product_id', Product::TAFTAN1401_TAJROBI_PACKAGE)->isNotEmpty();
    }

    public function tooreRaheAbrishamTajoribi1400()
    {
        return $this->orderproducts->where('product_id', Product::TOOR_ABRISHAM_1400_TAJROBRI)->isNotEmpty();
    }

    public function raheAbrisham99OmoomiPack()
    {
        return $this->orderproducts->where('product_id', Product::RAHE_ABRISHAM1401_PACK_OMOOMI)->isNotEmpty();
    }

    public function get3AOrderproducts()
    {
        return $this->orderproducts->whereIn('product_id', _3aExam::pluck('product_id')->toArray());
    }

    public function hasOrderproductViaSubsctiption(): bool
    {
        foreach ($this->orderproducts as $orderproduct) {
            $subscription = Subscription::query()->where('values', 'like', '%'.$orderproduct->id.'%')->get();
            if ($subscription->isNotEmpty()) {
                return true;
            }
        }

        return false;
    }

    public function updateOrderproductsSharecost()
    {
        //        ContentInComeJob::dispatch($this, true);
        /** @var /App/Orderproduct $orderproduct */
        foreach ($this->orderproducts as $orderproduct) {
            $orderproduct->setShareCost();
        }
    }

    public function updateOrderproductsTmpcost()
    {
        foreach ($this->orderproducts as $orderproduct) {
            /** @var Orderproduct $orderproduct */
            $orderproduct->setTmpFinalCost();
        }
    }

    public function resetCoupon()
    {
        $coupon = $this->coupon;
        if (isset($coupon)) {
            $newOrder = $this->restoreCoupon($coupon);
            $coupon = $newOrder->coupon_info2;
            $coupon = Arr::get($coupon, 0);
        }

        return $coupon;
    }

    /**
     * @param    $couponValidationStatus
     * @param    $order
     * @param  Coupon|null  $coupon
     * @return mixed
     */
    public function restoreCoupon(Coupon $coupon): Order
    {
        $couponValidationStatus = $coupon->validateCoupon();
        if (! (! Coupon::isOK($couponValidationStatus) && ! Coupon::isFinished($couponValidationStatus))) {
            return $this;
        }
        $this->detachCoupon();
        if ($this->updateWithoutTimestamp()) {
            $coupon->decreaseUseNumber();
            $coupon->update();
        }

        $this->update();

        return $this;
    }

    public function hasArashPack()
    {
        return $this->orderproducts->whereIn('product_id', Product::ARASH_PACK_PRODUCTS_ARRAY)->count();
    }

    public function hasArashEkhtesasiPack()
    {
        return $this->orderproducts->whereIn('product_id',
            [Product::ARASH_PACK_RITAZI_1400, Product::ARASH_PACK_TAJROBI_1400])->count();
    }

    public function hasArash()
    {
        return $this->orderproducts->whereIn('product_id', Product::ARASH_PRODUCTS_ARRAY)->count();
    }

    public function checkContentPaymentStatus(Content $content, Contentset $set)
    {
        $installmentPaidRatio = $this->getPaidRatio();
        $contentCount = $set->contents->count();
        $releasedContent = $set->contents->take(floor($installmentPaidRatio * $contentCount));

        return $releasedContent->contains($content);
    }

    public function scopePaidAndClosed(Builder $builder): Builder
    {
        return $builder->whereIn('orderstatus_id', self::getDoneOrderStatus())
            ->whereIn('paymentstatus_id', self::getDoneOrderPaymentStatus());
    }

    public static function getDoneOrderStatus(): array
    {
        return [config('constants.ORDER_STATUS_CLOSED'), config('constants.ORDER_STATUS_POSTED')];
    }

    public static function getDoneOrderPaymentStatus(): array
    {
        return [
            config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
        ];
    }

    public function scopeClosed(Builder $builder): Builder
    {
        return $builder->whereIn('orderstatus_id', self::getDoneOrderStatus());
    }

    public function scopeInDebt(Builder $builder): Builder
    {
        return $builder->where('paymentstatus_id', self::getInDebtPaymentStatus());
    }

    public static function getInDebtPaymentStatus(): array
    {
        return [config('constants.PAYMENT_STATUS_INDEBTED'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')];
    }

    public function scopePaid(Builder $builder): Builder
    {
        return $builder->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'));
    }

    public function scopeCompletedAfter(Builder $builder, string $dateTime): Builder
    {
        return $builder->where('completed_at', '>=', $dateTime);
    }

    public function scopeBetween(Builder $builder, string $startDate, string $endDate): Builder
    {
        return $builder->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeCreatedBy(Builder $query, array $creators): Builder
    {
        if (count($creators) === 0) {
            return $query;
        }

        return $query->whereHas('activities', function ($builder) use ($creators) {
            $builder->whereIn('causer_id', $creators)
                ->forEvent('created');
        });
    }

    public function scopeFilter(Builder $query, array $filterCases)
    {
        foreach ($filterCases as $column => $value) {
            $query->where($column, $value);
        }

        return $query;
    }

    public function checkAutomaticDonation()
    {
        if ($this->automatic_donation || $this->hasTheseProducts([Product::DONATE_PRODUCT_5_HEZAR])) {
            return null;
        }

        $donateProduct = Product::find(Product::DONATE_PRODUCT_5_HEZAR);

        if (! isset($donateProduct)) {
            return null;
        }

        $this->update([
            'automatic_donation' => true,
        ]);

        $oldOrderproduct = $this->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->where('product_id', '=', $donateProduct->id)
            ->onlyTrashed()
            ->get();

        if ($oldOrderproduct->isNotEmpty()) {
            $deletedOrderproduct = $oldOrderproduct->first();
            $deletedOrderproduct->restore();

            return null;
        }

        OrderproductRepo::createBasicOrderproduct(
            $this->id,
            $donateProduct->id,
            $donateProduct->basePrice
        );
    }
}
