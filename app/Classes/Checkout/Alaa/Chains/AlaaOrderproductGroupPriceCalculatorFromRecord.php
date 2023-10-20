<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/4/2018
 * Time: 2:56 PM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderproductGroupPriceCalculatorFromRecord;
use App\Classes\Abstracts\Pricing\OrderproductPriceCalculator;
use App\Classes\Pricing\Alaa\AlaaOrderproductPriceCalculator;
use App\Models\Orderproduct;
use Illuminate\Support\Collection;

class AlaaOrderproductGroupPriceCalculatorFromRecord extends OrderproductGroupPriceCalculatorFromRecord
{
    public const MODE = OrderproductPriceCalculator::ORDERPRODUCT_CALCULATOR_MODE_CALCULATE_FROM_RECORD;

    protected function getOrderproductGroupPrice(Collection $orderproductsToCalculateFromRecord)
    {
        foreach ($orderproductsToCalculateFromRecord as $orderproduct) {
            $priceInfo = $this->getOrderproductPrice($orderproduct);
            $orderproductsToCalculateFromRecord->setNewPriceForItem($orderproduct, $priceInfo);
        }

        return $orderproductsToCalculateFromRecord;
    }

    /**
     * Gets Orderproduct price
     *
     * @param  Orderproduct  $orderproduct
     *
     * @return mixed
     */
    private function getOrderproductPrice(Orderproduct $orderproduct)
    {
        $orderproductCalculator = new AlaaOrderproductPriceCalculator($orderproduct);
        $orderproductCalculator->setMode(self::MODE);

        return $orderproductCalculator->getPrice();
    }
}
