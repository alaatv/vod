<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/6/2018
 * Time: 10:23 AM
 */

namespace App\Classes\Abstracts\Checkout;

use PHPUnit\Framework\Exception;

abstract class OrderCouponCalculatorBasedOnPercentage extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $couponDiscountPercentage = $cashier->getOrderCouponDiscountPercentage();
        $temporaryTotalRawPriceWhichHasDiscount = $cashier->getTemporaryTotalPriceWithDiscount();

        if (!isset($temporaryTotalRawPriceWhichHasDiscount)) {
            throw new Exception('Temporary total price with discount has not been set');
        }

        if (!isset($couponDiscountPercentage)) {
            throw new Exception('Coupon discount percentage has not been set');
        }

        $totalPriceWithDiscount = $this->calculateDiscount($couponDiscountPercentage,
            $temporaryTotalRawPriceWhichHasDiscount);

        $cashier->setTotalPriceWithDiscount($totalPriceWithDiscount);

        return $this->next($cashier);
    }

    /**
     * Calculates discount for passed price and coupon discount type
     *
     * @param $couponDiscountPercentage
     * @param $totalRawPriceWhichHasDiscount
     *
     * @return int
     */
    abstract protected function calculateDiscount($couponDiscountPercentage, $totalRawPriceWhichHasDiscount): int;
}
