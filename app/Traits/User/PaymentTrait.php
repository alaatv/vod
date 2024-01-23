<?php

namespace App\Traits\User;

use App\Models\Bankaccount;
use App\Models\Order;
use App\Models\Ordermanagercomment;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\OrderRepo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Cache;

trait PaymentTrait
{
    protected $numberOfProducts_cache;

    /*
    |--------------------------------------------------------------------------
    | relations
    |--------------------------------------------------------------------------
    */

    public function ordermanagercomments()
    {
        return $this->hasMany(Ordermanagercomment::class);
    }

    public function bankaccounts()
    {
        return $this->hasMany(Bankaccount::class);
    }

    public function getNumberOfProductsInBasketAttribute()
    {
        if (! is_null($this->numberOfProducts_cache)) {
            return $this->numberOfProducts_cache;
        }
        $this->numberOfProducts_cache = $this->getOpenOrderOrCreate()->numberOfProducts;

        return $this->numberOfProducts_cache;
    }

    public function getOpenOrderOrCreate($isInInstalment = 0, $seller = 1): Order
    {
        return $this->openOrder($isInInstalment, $seller) ?? OrderRepo::createOpenOrder($this->id, $isInInstalment,
            $seller);
    }

    public function openOrder($isInInstalment, $seller)
    {
        return $this->hasMany(Order::class)
            ->where('orderstatus_id', config('constants.ORDER_STATUS_OPEN'))
            ->where('isInInstalment', $isInInstalment)
            ->where('seller', $seller)
            ->orderBy('id', 'asc')
            ->take(1)
            ->get()
            ->first();

    }

    public function orderproducts()
    {
        return $this->hasManyThrough(Orderproduct::class, Order::class);
    }

    public function closedorderproducts()
    {
        return $this->hasManyThrough(Orderproduct::class, Order::class)
            ->whereNotIn('orders.orderstatus_id', Order::OPEN_ORDER_STATUSES);
    }

    /**
     * Retrieve only order ralated transactions of this user
     */
    public function walletTransactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }

    /**
     * Retrieve all transactions of this user
     */
    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }

    public function getClosedOrders($pageNumber = 1, int $seller = 1)
    {
        $user = $this;
        $key = 'user:closedOrders:page-'.$pageNumber.':'.$user->cacheKey();

        return Cache::tags([
            'user', 'order', 'closedOrder', 'user_'.$user->id, 'user_'.$user->id.'_closedOrders',
        ])->remember($key, config('constants.CACHE_10'), function () use ($user, $pageNumber, $seller) {
            $orders = $user->closedOrders()
                ->where('seller', $seller)
                ->orderBy('completed_at', 'desc')
                ->paginate(10, ['*'], 'orders', $pageNumber);

            $path = parse_url(route('web.user.orders'), PHP_URL_PATH);
            $orders->withPath($path);

            return $orders;
        });
    }

    /**
     * Get user's orders that he is allowed to see
     */
    public function closedOrders(): HasMany
    {
        return $this->orders()
            ->whereNotIn('orderstatus_id', Order::OPEN_ORDER_STATUSES);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getClosedOrdersAttribute()
    {
        $user = $this;
        $key = 'user:closedOrders:'.$user->cacheKey();

        return Cache::tags([
            'user', 'order', 'closedOrder', 'user_'.$user->id, 'user_'.$user->id.'_closedOrders',
        ])->remember($key, config('constants.CACHE_10'), function () use ($user) {
            return $user->closedOrders()
                ->orderBy('completed_at', 'desc')
                ->get();
        });
    }

    public function getClosedOrdersForAPIV2($pageNumber = 1)
    {
        $user = $this;
        $seller = request()->input('seller', config('constants.ALAA_SELLER'));
        $key = 'user:getClosedOrdersForAPIV2:page-'.$pageNumber.'-'.$seller.':'.$user->cacheKey();

        return Cache::tags([
            'user', 'order', 'closedOrder', 'user_'.$user->id, 'user_'.$user->id.'_closedOrders',
        ])->remember($key, config('constants.CACHE_10'), function () use ($user, $pageNumber, $seller) {
            return $user->closedOrders()
                ->whereDoesntHave('orderproducts', function ($q) {
                    $q->where('product_id', Product::COUPON_PRODUCT);
                })
                ->where('seller', $seller)
                ->orderBy('completed_at', 'desc')
                ->paginate(100, ['*'], 'orders', $pageNumber);
        });
    }

    public function getTransactionsForAPIV2($pageNumber = 1)
    {
        $user = $this;
        $key = 'user:getTransactionsForAPIV2:page-'.$pageNumber.':'.$user->cacheKey();

        return Cache::tags(['user', 'transaction', 'user_'.$user->id, 'user_'.$user->id.'_transactions'])
            ->remember($key, config('constants.CACHE_60'), function () use ($user, $pageNumber) {
                return $user->getShowableTransactions()
                    ->orderBy('completed_at', 'desc')
                    ->paginate(100, ['*'], 'orders', $pageNumber);
            });
    }

    /**
     * Gets user's transactions that he is allowed to see
     */
    public function getShowableTransactions(): HasManyThrough
    {
        $showableTransactionStatuses = [
            config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
            config('constants.TRANSACTION_STATUS_ARCHIVED_SUCCESSFUL'),
            config('constants.TRANSACTION_STATUS_PENDING'),
        ];
        $transactions = $this->orderTransactions()
            ->whereDoesntHave('parents')
            ->where(function ($q) use ($showableTransactionStatuses) {
                $q->whereIn('transactionstatus_id', $showableTransactionStatuses);
            });

        return $transactions;
    }

    /**
     * Retrieve only order ralated transactions of this user
     */
    public function orderTransactions()
    {
        return $this->hasManyThrough(Transaction::class, Order::class);
    }

    public function getInstallmentsForAPIV2($pageNumber = 1)
    {
        $user = $this;
        $key = 'user:getInstallmentsForAPIV2:page-'.$pageNumber.':'.$user->cacheKey();

        return Cache::tags(['user', 'installment', 'user_'.$user->id, 'user_'.$user->id.'_installments'])
            ->remember($key, config('constants.CACHE_60'), function () use ($user, $pageNumber) {
                return $user->getInstalments()
                    ->orderBy('completed_at', 'desc')
                    ->paginate(100, ['*'], 'orders', $pageNumber);
            });
    }

    /**
     * Gets user's instalments
     */
    public function getInstalments(): HasManyThrough
    {
        //ToDo : to be tested
        return $this->orderTransactions()
            ->whereDoesntHave('parents')
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_UNPAID'));
    }

    public function openOrders(): HasMany
    {
        return $this->orders()
            ->whereIn('orderstatus_id', Order::OPEN_ORDER_STATUSES);
    }
}
