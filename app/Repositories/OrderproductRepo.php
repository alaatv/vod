<?php


namespace App\Repositories;


use App\Models\Orderproduct;
use Illuminate\Database\Eloquent\Builder;

class OrderproductRepo
{
    public const NOT_CHECKEDOUT_ORDERPRODUCT = 'unchecked';
    public const CHECKEDOUT_ORDERPRODUCT = 'checked';
    public const CHECKOUT_ALL = 'all';

    public static function refreshOrderproductTmpPrice(
        Orderproduct $orderproduct,
        int $tmpFinal,
        int $tmpExtraCost
    ): bool {
        return $orderproduct->update([
            'tmp_final_cost' => $tmpFinal,
            'tmp_extra_cost' => $tmpExtraCost,
        ]);
    }

    public static function getCollectionOfOrderProductsByIds(array $ids)
    {
        return Orderproduct::whereIn('id', $ids);
    }

    public static function refreshOrderproductTmpShare(Orderproduct $orderproduct, $share): bool
    {
        return $orderproduct->updateWithoutTimestamp([
            'tmp_share_order' => $share,
        ]);
    }

    public static function getPurchasedOrderproducts
    (
        array $productIds = [],
        string $since = null,
        string $till = null,
        string $checkoutMode = 'all',
        array $orderProductType = null,
        array $users = null,
        array $paymentStatusType = [],
        int $gateWay = null,
        bool $includeOnlyNoneZeroCosts = false,
    ): Builder {
        if (isset($orderProductType) && empty($orderProductType)) {
            $orderProductType = [
                config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
                config('constants.ORDER_PRODUCT_GIFT'),
            ];
        }
        if (isset($paymentStatusType) && empty($paymentStatusType)) {
            $paymentStatusType = [
                config('constants.PAYMENT_STATUS_PAID'),
                config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
                config('constants.PAYMENT_STATUS_INDEBTED'),
            ];
        }

        $orderproducts = Orderproduct::query();

        if ($includeOnlyNoneZeroCosts) {
            $orderproducts->where('cost', '<>', 0);
        }


        $orderproducts->whereIn('orderproducttype_id', $orderProductType)
            ->whereHas('order', function ($q) use ($since, $till, $users, $paymentStatusType, $gateWay) {
                $q
//                    ->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                    ->whereIn('paymentstatus_id', $paymentStatusType);

                if (isset($users) && !empty($users)) {
                    $q->whereIn('user_id', $users);
                }
                if (isset($since)) {
                    $q->where('completed_at', '>=', $since);
                }

                if (isset($till)) {
                    $q->where('completed_at', '<=', $till);
                }

                if (isset($gateWay)) {
                    $q->whereHas('transactions', function ($transactionQuery) use ($gateWay) {
                        $transactionQuery->where('transactionstatus_id',
                            config('constants.TRANSACTION_STATUS_SUCCESSFUL'))->where('transactiongateway_id',
                            $gateWay);
                    })->whereDoesntHave('transactions', function ($transactionQuery) use ($gateWay) {
                        $transactionQuery->where('transactionstatus_id',
                            config('constants.TRANSACTION_STATUS_SUCCESSFUL'))->where('transactiongateway_id', '!=',
                            $gateWay);
                    });
                }
            });
        if (!empty($productIds)) {
            $orderproducts->whereIn('product_id', $productIds);
        }

        if ($checkoutMode == 'checked') {
            $orderproducts->where('checkoutstatus_id', config('constants.ORDERPRODUCT_CHECKOUT_STATUS_PAID'));
        } else {
            if ($checkoutMode == 'unchecked') {
                $orderproducts->where(function ($q2) {
                    $q2->where('checkoutstatus_id', config('constants.ORDERPRODUCT_CHECKOUT_STATUS_UNPAID'))
                        ->orWhereNull('checkoutstatus_id');
                });
            }
        }

        return $orderproducts;
    }

    public static function filterOrderproductsOfOrder(array $orderIds, array $productIds = [])
    {
        $query = Orderproduct::query()->whereIn('order_id', $orderIds);

        if (!empty($productIds)) {
            $query->whereIn('product_id', $productIds);
        }

        return $query;
    }

    public static function createGiftOrderproduct(int $orderId, int $giftId, $giftCost)
    {
        return Orderproduct::create([
            'orderproducttype_id' => config('constants.ORDER_PRODUCT_GIFT'),
            'order_id' => $orderId,
            'product_id' => $giftId,
            'cost' => $giftCost,
            'discountPercentage' => 100,
        ]);
    }

    public static function createBasicOrderproduct(
        int $orderId,
        int $productId,
        $finalPrice,
        $tempFinalPrice = null,
        $includedInCoupon = 0,
        $discountPercentage = 0,
        $includedInInstalments = 0
    ) {
        return Orderproduct::Create([
            'order_id' => $orderId,
            'product_id' => $productId,
            'cost' => $finalPrice,
            'tmp_final_cost' => $tempFinalPrice,
            'orderproducttype_id' => config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
            'includedInCoupon' => $includedInCoupon,
            'discountPercentage' => $discountPercentage,
            'includedInInstalments' => $includedInInstalments,
        ]);
    }

    public static function createLockedOrderproduct(
        int $orderId,
        int $productId,
        $finalPrice,
        $tempFinalPrice = null,
        $includedInCoupon = 0,
        $discountPercentage = 0,
        $includedInInstalments = 0
    ) {
        return Orderproduct::Create([
            'order_id' => $orderId,
            'product_id' => $productId,
            'cost' => $finalPrice,
            'tmp_final_cost' => $tempFinalPrice,
            'orderproducttype_id' => config('constants.ORDER_PRODUCT_LOCKED'),
            'includedInCoupon' => $includedInCoupon,
            'discountPercentage' => $discountPercentage,
            'includedInInstalments' => $includedInInstalments,
        ]);
    }

    public static function createChangeOrderproduct(
        int $orderId,
        int $productId,
        $finalPrice,
        $tempFinalPrice = null,
        $includedInCoupon = 0,
        $discountPercentage = 0,
        $includedInInstalments = 0
    ) {
        return Orderproduct::Create([
            'order_id' => $orderId,
            'product_id' => $productId,
            'cost' => $finalPrice,
            'tmp_final_cost' => $tempFinalPrice,
            'orderproducttype_id' => config('constants.ORDER_PRODUCT_CHANGE'),
            'includedInCoupon' => $includedInCoupon,
            'discountPercentage' => $discountPercentage,
            'includedInInstalments' => $includedInInstalments,
        ]);
    }

    public static function createHiddenOrderproduct(
        int $orderId,
        int $productId,
        $finalPrice,
        $tempFinalPrice = null,
        $includedInCoupon = 0,
        $discountPercentage = 0,
        $includedInInstalments = 0,
        $instalmentQty = null,
        $paidPercent = null
    ) {
        return Orderproduct::Create([
            'order_id' => $orderId,
            'product_id' => $productId,
            'cost' => $finalPrice,
            'tmp_final_cost' => $tempFinalPrice,
            'orderproducttype_id' => config('constants.ORDER_PRODUCT_HIDDEN'),
            'includedInCoupon' => $includedInCoupon,
            'discountPercentage' => $discountPercentage,
            'includedInInstalments' => $includedInInstalments,
            'instalmentQty' => $instalmentQty,
            'paidPercent' => $paidPercent,
        ]);
    }

    public static function findItemsWithWorngDiscount($orderProducts, $discount, $relations = [])
    {
        return Orderproduct::with($relations)
            ->whereIn('id', $orderProducts)
            ->where('discountPercentage', '<', $discount);
    }

    public static function getByProduct(array $products)
    {
        return Orderproduct::whereIn('product_id', $products);
    }

    public static function excludeByProduct(array $products)
    {
        return Orderproduct::whereNotIn('product_id', $products);
    }
}
