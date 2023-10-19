<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/2/2018
 * Time: 11:16 AM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderDiscountCostAmountCalculator;

class AlaaOrderDiscountCostAmountCalculator extends OrderDiscountCostAmountCalculator
{
    protected function calculateOrderDiscount($totalPrice, $discount)
    {
        return max($totalPrice - $discount, 0);
    }
}
