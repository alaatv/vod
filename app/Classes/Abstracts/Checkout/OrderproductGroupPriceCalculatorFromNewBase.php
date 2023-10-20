<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 4:09 PM
 */

namespace App\Classes\Abstracts\Checkout;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Exception;

abstract class OrderproductGroupPriceCalculatorFromNewBase extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $orderproductsToCalculateFromNewBase = $cashier->getRawOrderproductsToCalculateFromBase();
        if (!isset($orderproductsToCalculateFromNewBase)) {
            throw new Exception('Orderproducts to recalculate have not been set');
        }

        $calculatedOrderproductsFromNewBase = $this->getOrderproductGroupPrice($orderproductsToCalculateFromNewBase);

        $calculatedOrderproducts = $cashier->getCalculatedOrderproducts();
        if (isset($calculatedOrderproducts)) {
            $calculatedOrderproducts = $calculatedOrderproducts->merge($calculatedOrderproductsFromNewBase);
        } else {
            $calculatedOrderproducts = $calculatedOrderproductsFromNewBase;
        }

        $cashier->setCalculatedOrderproducts($calculatedOrderproducts);

        return $this->next($cashier);
    }

    /**
     * Gets price for a group of Orderproducts
     *
     * @param  Collection  $orderproductsToCalculateFromNewBase
     *
     * @return mixed
     */
    abstract protected function getOrderproductGroupPrice(Collection $orderproductsToCalculateFromNewBase): Collection;
}
