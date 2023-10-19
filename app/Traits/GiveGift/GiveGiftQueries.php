<?php


namespace App\Traits\GiveGift;





use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait GiveGiftQueries
{
    public static function selectOrders($action)
    {
        switch ($action) {
            case self::ARASH:
                return self::generalOrderSelection(self::ARASH);
            case self::AZMOON:
                return self::selectUsersFor4K();
            case self::TITAN_ADABIYAT:
                return self::selectOrderForTitanAdabiyat();
            default:
                return null;
        }
    }

    /**
     * @description select Done and Payed orders that has $products which are not gift
     * @param  array  $products  id of products that should be in order.
     * @return  Collection $orders.
     **/
    public static function generalOrderSelection(?string $plan)
    {
        $products = self::PLANS[$plan][self::PRODUCTS];

        $orders = Order::query()->whereIn('orderstatus_id', Order::getDoneOrderStatus())
            ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
            ->whereHas('orderproducts', function ($q) use ($products) {
                $q->where('orderproducttype_id', '<>', config('constants.ORDER_PRODUCT_GIFT'))
                    ->whereIn('product_id', $products);
            })->get();
        return $orders;
    }

    public static function selectUsersFor4K()
    {
        return User::query()->whereHas('orders', function ($q) {
            $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
                ->whereHas('orderproducts', function ($q2) {
                    $q2->whereIn('product_id', Product::ARASH_PRODUCTS_ARRAY);
                });
        })->get();
    }

    public static function selectOrderForTitanAdabiyat()
    {
        return Order::query()
            ->where('created_at', '>=', '2020-08-22 00:00:00')
            ->whereIn('orderstatus_id', Order::getDoneOrderStatus())
            ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
            ->whereHas('transactions', function ($q) {
                $q->where('cost', '>', 0)
                    ->whereNull('wallet_id')
                    ->where('paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
                    ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'));
            })->get();
    }

}
