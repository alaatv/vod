<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/2/2018
 * Time: 12:37 PM
 */

namespace App\Classes\Pricing\Alaa;

use App\Classes\Abstracts\Pricing\OrderproductPriceCalculator;

class AlaaOrderproductPriceCalculator extends OrderproductPriceCalculator
{
    public function getPrice()
    {
        return $this->getOrderproductPrice();
    }
}
