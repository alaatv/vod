<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/27/2018
 * Time: 11:38 AM
 */

namespace App\Traits\Cashier;

trait CashierOrderDiscountUnit
{
    protected $orderDiscountCostAmount;

    /**
     * @return mixed
     */
    public function getOrderDiscountCostAmount()
    {
        return $this->orderDiscountCostAmount;
    }

    /**
     * @param  mixed  $orderDiscountCostAmount
     *
     * @return mixed
     */
    public function setOrderDiscountCostAmount($orderDiscountCostAmount)
    {
        $this->orderDiscountCostAmount = $orderDiscountCostAmount;

        return $this;
    }
}
