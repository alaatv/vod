<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 5:37 PM
 */

namespace App\Classes\Abstracts\Checkout;

use PHPUnit\Framework\Exception;

abstract class OrderCouponCalculatorBasedOnCostAmount extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $couponDiscountCostAmount = $cashier->getOrderCouponDiscountCostAmount();
        $temporaryTotalRawPriceWhichHasDiscount = $cashier->getTemporaryTotalPriceWithDiscount();
        if (!isset($temporaryTotalRawPriceWhichHasDiscount)) {
            throw new Exception('Temporary total price with discount has not been set');
        }

        if (!isset($couponDiscountCostAmount)) {
            throw new Exception('Coupon discount cost amount has not been set');
        }

        $totalPriceWithDiscount = $this->calculateDiscount($couponDiscountCostAmount,
            $temporaryTotalRawPriceWhichHasDiscount);

        $cashier->setTotalPriceWithDiscount($totalPriceWithDiscount);

        return $this->next($cashier);
    }

    /**
     * Calculates discount for passed price and coupon discount type
     *
     * @param $couponDiscountCostAmount
     * @param $totalRawPriceWhichHasDiscount
     *
     * @return int
     */
    abstract protected function calculateDiscount($couponDiscountCostAmount, $totalRawPriceWhichHasDiscount): int;
}
