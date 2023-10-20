<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/3/2018
 * Time: 4:34 PM
 */

namespace App\Collection;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class OrderCollections extends Collection
{
    /**
     * @return BaseCollection
     */
    public function getCoupons(): BaseCollection
    {
        $items = $this;
        $result = collect();

        foreach ($items as $order) {
            if (!isset($order->coupon_id)) {
                continue;
            }
            $orderCoupon = $order->coupon_discount_type;
            if ($orderCoupon === false) {
                continue;
            }
            if ($orderCoupon['type'] == config('constants.DISCOUNT_TYPE_PERCENTAGE')) {
                $result->put($order->id, [
                    'caption' => 'کپن '.$order->coupon->name.' با '.$orderCoupon['discount'].' % تخفیف',
                ]);
            } else {
                if ($orderCoupon['type'] == config('constants.DISCOUNT_TYPE_COST')) {
                    $result->put($order->id, [
                        'caption' => 'کپن '.$order->coupon->name.' با '.number_format($orderCoupon['discount']).' تومان تخفیف',
                    ]);
                }
            }

        }

        return $result;
    }

    /**
     * @return int
     */
    public function getNumberOfProductsInThisOrderCollection(): int
    {
        $sum = 0;
        foreach ($this as $order) {
            $sum += $order->numberOfProducts;
        }

        return $sum;
    }
}
