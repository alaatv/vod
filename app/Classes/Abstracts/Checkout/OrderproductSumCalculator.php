<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 5:56 PM
 */

namespace App\Classes\Abstracts\Checkout;

use App\Models\Order;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Exception;

abstract class OrderproductSumCalculator extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $calculatedOrderproducts = $cashier->getCalculatedOrderproducts();
        if (!isset($calculatedOrderproducts)) {
            throw new Exception('Calculated orderproducts have not been set');
        }

        $order = $calculatedOrderproducts->first()?->order;
        $priceSumInfo = $this->calculateSum($calculatedOrderproducts, $order);

        $cashier->setTotalRawPriceWhichDoesntHaveDiscount($priceSumInfo['totalRawPriceWhichDoesntHaveDiscount'])
            ->setTotalRawPriceWhichHasDiscount($priceSumInfo['totalRawPriceWhichHasDiscount'])
            ->setTotalPriceWithDiscount($priceSumInfo['totalRawPriceWhichHasDiscount'])
            ->setSumOfOrderproductsRawCost($priceSumInfo['sumOfOrderproductsRawCost'])
            ->setSumOfOrderproductsCustomerCost($priceSumInfo['sumOfOrderproductsCustomerCost']);

        return $this->next($cashier);
    }

    /**
     * Calculates the sum price for passed Orderproduct collection
     *
     * @param  Collection  $calculatedOrderproducts
     * @param $order
     * @return array
     */
    abstract protected function calculateSum(Collection $calculatedOrderproducts, ?Order $order): array;
}
