<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 4:11 PM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderPriceCalculator;

class AlaaOrderPriceCalculator extends OrderPriceCalculator
{
    protected function calculateOrderPrice($totalRawPriceWhichDoesntHaveDiscount, $totalPriceWithDiscount): int
    {
        return $totalRawPriceWhichDoesntHaveDiscount + intval(round($totalPriceWithDiscount), 0);
    }
}
