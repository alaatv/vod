<?php


namespace App\Classes;


use App\Models\Coupon;
use App\Models\Order;

class CouponSubmitter
{

    private $order;

    /**
     * CouponSubmitter constructor.
     *
     * @param $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param  Coupon  $coupon
     *
     * @return bool
     */
    public function submit(Coupon $coupon): bool
    {
        $oldCoupon = $this->order->coupon;
        if (!isset($oldCoupon)) {
            $coupon->increaseUseNumber()->update();
            $orderUpdateResult = $this->order->attachCoupon($coupon)->updateWithoutTimestamp();
            if ($orderUpdateResult) {
                return true;
            }
            $coupon->decreaseUseNumber()->update();
            return false;
        }

        if ($oldCoupon->id == $coupon->id) {
            return true;
        }

        $oldCoupon->decreaseUseNumber()->update();
        $coupon->increaseUseNumber()->update();
        $orderUpdateResult = $this->order->attachCoupon($coupon)->updateWithoutTimestamp();
        if ($orderUpdateResult) {
            return true;
        }

        $oldCoupon->increaseUseNumber()->update();
        $coupon->decreaseUseNumber()->update();

        // TODO : THIS METHOD HAS TO RETURN FALSE
        return true;
    }
}
