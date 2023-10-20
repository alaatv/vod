<?php

namespace App\Classes\Abstracts\Checkout;

use PHPUnit\Framework\Exception;

abstract class OrderReferralCodeCalculator extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $referralCodediscount = $cashier->getOrderReferralCodeDiscount();
        $referralCodediscountType = $cashier->getOrderReferralCodeDiscountType();
        $temporaryFinalPrice = $cashier->getTemporaryFinalPrice();
        if (!isset($referralCodediscount)) {
            throw new Exception('Order referral code has not been set');
        }

        if (!isset($temporaryFinalPrice)) {
            throw new Exception('Order temporary final price has not been set');
        }

        $orderFinalPrice =
            $this->calculateReferralCodeDiscount($temporaryFinalPrice, $referralCodediscount,
                $referralCodediscountType);
        $cashier->setFinalPrice($orderFinalPrice);

        return $this->next($cashier);
    }

    abstract protected function calculateReferralCodeDiscount($totalPrice, $discount, $referralCodediscountType);
}
