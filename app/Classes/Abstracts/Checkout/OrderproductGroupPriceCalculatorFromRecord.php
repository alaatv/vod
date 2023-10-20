<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/4/2018
 * Time: 1:41 PM
 */

namespace App\Classes\Abstracts\Checkout;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Exception;

abstract class OrderproductGroupPriceCalculatorFromRecord extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $orderproductsToCalculateFromRecord = $cashier->getRawOrderproductsToCalculateFromRecord();
        if (!isset($orderproductsToCalculateFromRecord)) {
            throw new Exception('Orderproducts to calculate from records have not been set');
        }

        $calculatedOrderproductsFromRecord = $this->getOrderproductGroupPrice($orderproductsToCalculateFromRecord);

        $calculatedOrderproducts = $cashier->getCalculatedOrderproducts();
        if (isset($calculatedOrderproducts)) {
            $calculatedOrderproducts = $calculatedOrderproducts->merge($calculatedOrderproductsFromRecord);
        } else {
            $calculatedOrderproducts = $calculatedOrderproductsFromRecord;
        }

        $cashier->setCalculatedOrderproducts($calculatedOrderproducts);

        return $this->next($cashier);
    }

    /**
     * Gets price for a group of Orderproducts
     *
     * @param  Collection  $orderproductsToCalculateFromRecord
     *
     * @return mixed
     */
    abstract protected function getOrderproductGroupPrice(Collection $orderproductsToCalculateFromRecord);
}
