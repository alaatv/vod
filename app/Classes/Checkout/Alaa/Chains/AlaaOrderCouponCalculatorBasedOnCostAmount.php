<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 5:38 PM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderCouponCalculatorBasedOnCostAmount;

class AlaaOrderCouponCalculatorBasedOnCostAmount extends OrderCouponCalculatorBasedOnCostAmount
{
    protected function calculateDiscount($couponDiscountCostAmount, $totalRawPriceWhichHasDiscount): int
    {
        return (int) (max($totalRawPriceWhichHasDiscount - $couponDiscountCostAmount, 0));
    }
}
