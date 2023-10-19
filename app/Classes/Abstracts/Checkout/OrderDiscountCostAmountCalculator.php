<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/2/2018
 * Time: 11:15 AM
 */

namespace App\Classes\Abstracts\Checkout;

use PHPUnit\Framework\Exception;

abstract class OrderDiscountCostAmountCalculator extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $discount = $cashier->getOrderDiscountCostAmount();
        $temporaryFinalPrice = $cashier->getTemporaryFinalPrice();
        if (!isset($discount)) {
            throw new Exception('Order discount has not been set');
        }

        if (!isset($temporaryFinalPrice)) {
            throw new Exception('Order temporary final price has not been set');
        }

        $orderFinalPrice = $this->calculateOrderDiscount($temporaryFinalPrice, $discount);

        $cashier->setFinalPrice($orderFinalPrice);

        return $this->next($cashier);
    }

    abstract protected function calculateOrderDiscount($totalPrice, $discount);
}
