<?php


namespace App\Repositories;


use App\Models\Activity;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class OrderRepo
{
    public static function createOpenOrder(int $userId, $isInInstalment, $seller): Order
    {
        return Order::create([
            'user_id' => $userId,
            'orderstatus_id' => config('constants.ORDER_STATUS_OPEN'),
            'paymentstatus_id' => config('constants.PAYMENT_STATUS_UNPAID'),
            'isInInstalment' => $isInInstalment,
            'seller' => $seller,
        ]);
    }

    public static function createBasicCompletedOrder(
        int $userId,
        ?int $paymentstatus_id = null,
        $costWithoutCoupon = null,
        $costWithCoupon = null,
        ?int $couponId = null,
        ?int $couponDiscount = 0,
        int $discount = 0,
        ?int $orderStatusId = null,
        ?int $seller = null,
    ): Order {
        return Order::create([
            'user_id' => $userId,
            //I did not put default value in the method signature because we can not use config() method in there
            // and since I have defined these values in constant file I dont want to duplicate them in order to be albe to use them here
            'orderstatus_id' => $orderStatusId ?? config('constants.ORDER_STATUS_CLOSED'),
            'paymentstatus_id' => $paymentstatus_id ?? config('constants.PAYMENT_STATUS_UNPAID'),
            'completed_at' => Carbon::now('Asia/Tehran'),
            'costwithoutcoupon' => $costWithoutCoupon,
            'cost' => $costWithCoupon,
            'coupon_id' => $couponId,
            'couponDiscount' => $couponDiscount,
            'discount' => $discount,
            'seller' => $seller ?? config('constants.ALAA_SELLER'),
        ]);
    }

    public static function orderStatusFilter($orders, $orderStatusesId)
    {
        return $orders->whereIn('orderstatus_id', $orderStatusesId);
    }

    public static function UserMajorFilter(Builder $orders, $majorsId)
    {
        if (in_array(0, $majorsId)) {
            $orders = $orders->whereHas('user', function ($q) use ($majorsId) {
                /** @var Builder $q */
                $q->whereDoesntHave('major');
            });
        } else {
            $orders = $orders->whereHas('user', function ($q) use ($majorsId) {
                /** @var Builder $q */
                $q->whereIn('major_id', $majorsId);
            });
        }

        return $orders;
    }

    public static function paymentStatusFilter($orders, $paymentStatusesId)
    {
        return $orders->whereIn('paymentstatus_id', $paymentStatusesId);
    }

    public static function findCouponOrder(int $userId, int $couponProductId)
    {
        return Order::where('user_id', $userId)
            ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_UNPAID'))
            ->whereHas('orderproducts', function ($q) use ($couponProductId) {
                $q->where('product_id', $couponProductId);
            })->first();
    }

    public static function findLogs(int $orderId)
    {
        try {
            return Activity::where('subject_type', Order::class)
                ->where('subject_id', $orderId);
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param  array|null  $products
     * @param  string|null  $from
     * @param  string|null  $to
     * @param  array|null  $paymentStatusIds
     * @param  Coupon|null  $coupon
     * @param  array|null  $orderStatusIds
     * @return Order|Builder
     */
    public static function generalOrderSelectionWithPayment(
        ?array $paymentStatusIds = null,
        ?array $products = null,
        ?string $from = null,
        ?string $to = null,
        ?Coupon $coupon = null,
        ?array $orderStatusIds = null
    ) {
        return self::generalOrderSelection($paymentStatusIds, $products, $from, $to, $coupon, $orderStatusIds)
            ->whereDoesntHave('transactions', function ($q) {
                return $q->whereNotIn('paymentmethod_id', [config('constants.PAYMENT_METHOD_WALLET')])
                    ->whereIn('transactionstatus_id', [config('constants.TRANSACTION_STATUS_SUCCESSFUL')]);
            });
    }

    /**
     * @param  array|null  $productIds
     * @param  string|null  $from
     * @param  string|null  $to
     * @param  array|null  $paymentStatusIds
     * @param  Coupon|null  $coupon
     * @param  array|null  $orderStatusIds
     * @return Order|Builder|\Illuminate\Database\Query\Builder
     */
    public static function generalOrderSelection(
        ?array $paymentStatusIds = null,
        ?array $productIds = null,
        ?string $from = null,
        ?string $to = null,
        ?Coupon $coupon = null,
        ?array $orderStatusIds = null
    ) {
        $orders = Order::query()
            ->whereIn('orderstatus_id', $orderStatusIds ?? Order::getDoneOrderStatus())
            ->whereIn('paymentstatus_id', $paymentStatusIds ?? Order::getDoneOrderPaymentStatus());

        if (is_array($productIds) && count($productIds)) {
            $orders->whereHas('orderproducts', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            });
        }

        if (!empty($from)) {
            $orders->where('completed_at', '>=', $from);
        }

        if (!empty($to)) {
            $orders->where('completed_at', '<=', $to);
        }

        if (!empty($coupon)) {
            $orders->where('coupon_id', $coupon->id);
        }

        return $orders;
    }

    public static function getBasePaymentAndOrderStatus(array $orderStatus, array $paymentStatus, array $productIds)
    {
        return Order::query()->whereIn('orderstatus_id', $orderStatus)
            ->whereIn('paymentstatus_id', $paymentStatus)
            ->whereHas('orderproducts', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            });
    }

    public static function generalOrderSelectionWithUser(array $productIds, array $usersId)
    {
        return self::generalOrderSelection(productIds: $productIds)->whereIn('user_id', $usersId);
    }

    public static function hasPurchased(User $user, array $productIds)
    {
        return Order::query()
            ->where('user_id', $user->id)
            ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
            ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
            ->whereHas('orderproducts', function ($q) use ($productIds) {
                $q->whereIn('product_id', $productIds);
            });
    }

    public static function getInstallmentallyOrders()
    {
        return Order::with('orderproducts')->where('paymentstatus_id', config('constants.PAYMENT_STATUS_INDEBTED'))
            ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'));
    }

    public static function getUserCompletedPaidOrders(
        ?User $user = null,
        array $relation = [],
        $completedAtSince = null,
        $completedAtTill = null
    ) {
        $orders = Order::paidAndClosed();

        if (isset($user)) {
            $orders->where('user_id', $user->id);
        }

        if (isset($completedAtSince)) {
            $orders->where('completed_at', '>=', $completedAtSince);
        }

        if (isset($completedAtTill)) {
            $orders->where('completed_at', '<=', $completedAtTill);
        }

        if (!empty($relation)) {
            $orders->with($relation);
        }

        return $orders;
    }

    public static function filterOrdersBaseCoupon(array $data): Builder
    {
        return Order::whereHas('coupon', function ($query) use ($data) {
            foreach ($data as $col => $details) {
                $operator = Arr::get($details, 'operator', '=');
                $value = Arr::get($details, 'value');
                if ($value) {
                    $query->where($col, $operator, $value);
                }
            }
        });
    }

    public static function ordersHasRelatedProduct($allRelatedProductIds, $interval = null)
    {
        return Order::with(['orderproducts'])
            ->whereHas('orderproducts', function ($query) use ($allRelatedProductIds) {
                $query->whereIn('product_id', $allRelatedProductIds);
            })
            ->when($interval, function ($query, $interval) {
                $query->where('completed_at', '>=', Carbon::now()->subDays($interval));
            })
            ->closed()
            ->whereIn('paymentstatus_id', [
                config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
                config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'), config('constants.PAYMENT_STATUS_INDEBTED')
            ]);
    }
}
