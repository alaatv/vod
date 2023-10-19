<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/5/2018
 * Time: 4:06 PM
 */

namespace App\Classes\Abstracts\Checkout;

use App\Models\Coupon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Exception;

abstract class OrderproductCouponChecker extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $orderproductsToCalculateFromNewBase = $cashier->getRawOrderproductsToCalculateFromBase();
        $coupon = $cashier->getOrderCoupon();
        if (!isset($orderproductsToCalculateFromNewBase)) {
            throw new Exception('Orderproducts to recalculate have not been set');
        }

        if (!isset($coupon)) {
            throw new Exception('Order coupon has not been set');
        }

        $checkedOrderproducts = $this->IsIncludedInCoupon($orderproductsToCalculateFromNewBase, $coupon);

        $cashier->setRawOrderproductsToCalculateFromBase($checkedOrderproducts);

        return $this->next($cashier);
    }

    /**
     * Checks whether orderproduct is included in coupon or not
     *
     * @param  Collection  $orderproducts
     * @param  Coupon  $coupon
     *
     * @return mixed
     */
    abstract protected function IsIncludedInCoupon(Collection $orderproducts, Coupon $coupon): Collection;
}
