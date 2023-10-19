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
use App\Models\Orderproduct;

class OrderproductCheckout extends CheckoutInvoker
{
    private $orderproduct;

    private $recalculate;

    /**
     * OrderCheckout constructor.
     *
     * @param  Orderproduct  $orderproduct
     * @param  bool  $recalculate
     */
    public function __construct(Orderproduct $orderproduct, bool $recalculate)
    {
        $this->orderproduct = $orderproduct;
        $this->recalculate = $recalculate;
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
        ];
    }

    protected function initiateCashier(): Cashier
    {
        $orderproductsToCalculateFromBase = new OrderproductCollection();
        $orderproductsToCalculateFromRecord = new OrderproductCollection();
        if ($this->recalculate) {
            $this->orderproduct->load('product', 'product.parents', 'userbons', 'attributevalues',
                'product.attributevalues');
            $orderproductsToCalculateFromBase = new OrderproductCollection([$this->orderproduct]);
        } else {
            $this->orderproduct->load('userbons', 'attributevalues');
            $orderproductsToCalculateFromRecord = new OrderproductCollection([$this->orderproduct]);
        }

        $alaaCashier = new AlaaCashier();
        $alaaCashier->setRawOrderproductsToCalculateFromBase($orderproductsToCalculateFromBase);
        $alaaCashier->setRawOrderproductsToCalculateFromRecord($orderproductsToCalculateFromRecord);

        return $alaaCashier;
    }
}
