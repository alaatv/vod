<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/3/2018
 * Time: 10:37 AM
 */

namespace App\Classes\Checkout\Alaa;

use App\Classes\Abstracts\Checkout\Cashier;
use App\Classes\Abstracts\Checkout\CheckoutInvoker;
use App\Collection\OrderproductCollection;

class GroupOrderproductCheckout extends CheckoutInvoker
{
    private $orderproducts;

    private $orderproductsToCalculateFromBaseIds;

    /**
     * OrderCheckout constructor.
     *
     * @param  OrderproductCollection  $orderproducts
     * @param  array  $orderproductsToCalculateFromBaseIds
     */
    public function __construct(OrderproductCollection $orderproducts, array $orderproductsToCalculateFromBaseIds = [])
    {
        $this->orderproducts = $orderproducts;
        $this->orderproductsToCalculateFromBaseIds = $orderproductsToCalculateFromBaseIds;
    }

    public function getChainClassesNameSpace(): string
    {
        return __NAMESPACE__."\\Chains";
    }

    /**
     * @return array
     */
    protected function fillChainArray(): array
    {
        return [
            'AlaaOrderproductGroupPriceCalculatorFromNewBase',
            'AlaaOrderproductGroupPriceCalculatorFromRecord',
            'AlaaOrderproductSumCalculator',
        ];
    }

    protected function initiateCashier(): Cashier
    {
        $orderproducts = $this->orderproducts;

        $orderproductsToCalculateFromBase = $orderproducts->whereIn('id', $this->orderproductsToCalculateFromBaseIds);
        $orderproductsToCalculateFromRecord = $orderproducts->whereNotIn('id',
            $this->orderproductsToCalculateFromBaseIds);

        $alaaCashier = new AlaaCashier();
        $alaaCashier->setRawOrderproductsToCalculateFromBase($orderproductsToCalculateFromBase);
        $alaaCashier->setRawOrderproductsToCalculateFromRecord($orderproductsToCalculateFromRecord);

        return $alaaCashier;
    }
}
