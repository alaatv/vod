<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/6/2018
 * Time: 10:30 AM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderCouponCalculatorBasedOnPercentage;

class AlaaOrderCouponCalculatorBasedOnPercentage extends OrderCouponCalculatorBasedOnPercentage
{
    protected function calculateDiscount($couponDiscountPercentage, $totalRawPriceWhichHasDiscount): int
    {
        return (int) ((1 - $couponDiscountPercentage) * $totalRawPriceWhichHasDiscount);
    }
}
