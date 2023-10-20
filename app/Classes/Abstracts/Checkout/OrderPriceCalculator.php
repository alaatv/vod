<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 4:09 PM
 */

namespace App\Classes\Abstracts\Checkout;

use PHPUnit\Framework\Exception;

abstract class OrderPriceCalculator extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $totalRawPriceWhichDoesntHaveDiscount = $cashier->getTotalRawPriceWhichDoesntHaveDiscount();
        $totalPriceWithDiscount = $cashier->getTotalPriceWithDiscount();
        if (!isset($totalRawPriceWhichDoesntHaveDiscount)) {
            throw new Exception('Total price which does not have coupon discount has not been set');
        }

        if (!isset($totalPriceWithDiscount)) {
            throw new Exception('Total price which has been calculated from coupon discount has not been set');
        }

        $totalPrice = $this->calculateOrderPrice($totalRawPriceWhichDoesntHaveDiscount, $totalPriceWithDiscount);

        $cashier->setTotalPrice($totalPrice);
        $cashier->setFinalPrice($totalPrice);

        return $this->next($cashier);
    }

    /**
     * Calculates final price for passed total price and discount
     *
     * @param $totalRawPriceWhichDoesntHaveDiscount
     * @param $totalPriceWithDiscount
     * @param $discount
     *
     * @return int
     */
    abstract protected function calculateOrderPrice(
        $totalRawPriceWhichDoesntHaveDiscount,
        $totalPriceWithDiscount
    ): int;
}
