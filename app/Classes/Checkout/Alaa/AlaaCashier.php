<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 10/11/2018
 * Time: 2:51 PM
 */

namespace App\Classes\Checkout\Alaa;

use App\Classes\Abstracts\Checkout\Cashier;

class AlaaCashier extends Cashier
{
    public function getPrice()
    {
        $priceInfo = [
            'order' => $this->order,
            'totalPriceInfo' => [
                'totalRawPriceWhichHasDiscount' => $this->totalRawPriceWhichHasDiscount,
                'totalRawPriceWhichDoesntHaveDiscount' => $this->totalRawPriceWhichDoesntHaveDiscount,
                'totalPriceWithDiscount' => $this->totalPriceWithDiscount,
                'sumOfOrderproductsRawCost' => $this->sumOfOrderproductsRawCost,
                'sumOfOrderproductsCustomerCost' => $this->sumOfOrderproductsCustomerCost,
                'totalPrice' => $this->totalPrice,
                'finalPrice' => $this->finalPrice,
                'payableAmountByWallet' => $this->payableAmountByWallet,
            ],
            'orderproductsInfo' => [
                'rawOrderproductsToCalculateFromBase' => $this->rawOrderproductsToCalculateFromBase,
                'rawOrderproductsToCalculateFromRecord' => $this->rawOrderproductsToCalculateFromRecord,
                'calculatedOrderproducts' => $this->calculatedOrderproducts,
            ],
        ];

//        return json_encode($priceInfo);
        return $priceInfo;
    }
}
