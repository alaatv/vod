<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 4:08 PM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderproductSumCalculator;
use App\Collection\OrderproductCollection;
use Illuminate\Support\Collection;

class AlaaOrderproductSumCalculator extends OrderproductSumCalculator
{
    /**
     * @param  Collection  $calculatedOrderproducts
     *
     * @return array
     */
    protected function calculateSum(Collection $calculatedOrderproducts, $order): array
    {
        /** @var OrderproductCollection $calculatedOrderproducts */

        $sumOfOrderproductsRawPrice = 0;
        $sumOfOrderproductsCustomerPrice = 0;
        $totalRawPriceWhichHasDiscount = 0;
        $totalRawPriceWhichDoesntHaveDiscount = 0;//totalRawPriceWhichDoesntHaveDiscount

        foreach ($calculatedOrderproducts as $orderproduct) {
            $orderproductPriceInfo = $calculatedOrderproducts->getNewPriceForItem($orderproduct);

            $orderproductPrice =
                $order?->coupon?->is_strict ? $orderproductPriceInfo['cost'] : $orderproductPriceInfo['totalCost'];
            $orderproductExtraPrice = $orderproductPriceInfo['extraCost'];
            $sumOfOrderproductsRawPrice += $orderproductPriceInfo['cost'];
            $sumOfOrderproductsCustomerPrice += $orderproductPriceInfo['customerCost'];

            if ($orderproduct->includedInCoupon == 1) {
                $totalRawPriceWhichHasDiscount += $orderproductPrice;
            } else {
                $totalRawPriceWhichDoesntHaveDiscount += $orderproductPrice;
            }

            $totalRawPriceWhichDoesntHaveDiscount += $orderproductExtraPrice;
            $sumOfOrderproductsRawPrice += $orderproductExtraPrice;
        }

        return [
            'totalRawPriceWhichHasDiscount' => $totalRawPriceWhichHasDiscount,
            'totalRawPriceWhichDoesntHaveDiscount' => $totalRawPriceWhichDoesntHaveDiscount,
            'sumOfOrderproductsRawCost' => $sumOfOrderproductsRawPrice,
            'sumOfOrderproductsCustomerCost' => $sumOfOrderproductsCustomerPrice,
        ];
    }
}
