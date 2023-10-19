<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/27/2018
 * Time: 11:49 AM
 */

namespace App\Traits\Cashier;

trait CashierOrderproductSumUnit
{
    protected $sumOfOrderproductsRawCost;

    protected $sumOfOrderproductsCustomerCost;

    /**
     * @return mixed
     */
    public function getSumOfOrderproductsRawCost()
    {
        return $this->sumOfOrderproductsRawCost;
    }

    /**
     * @param  mixed  $sumOfOrderproductsRawCost
     *
     * @return mixed
     */
    public function setSumOfOrderproductsRawCost($sumOfOrderproductsRawCost)
    {
        $this->sumOfOrderproductsRawCost = $sumOfOrderproductsRawCost;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSumOfOrderproductsCustomerCost()
    {
        return $this->sumOfOrderproductsCustomerCost;
    }

    /**
     * @param  mixed  $sumOfOrderproductsCustomerCost
     *
     * @return mixed
     */
    public function setSumOfOrderproductsCustomerCost($sumOfOrderproductsCustomerCost)
    {
        $this->sumOfOrderproductsCustomerCost = $sumOfOrderproductsCustomerCost;

        return $this;
    }
}
