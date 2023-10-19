<?php

namespace App\Traits\Cashier;

trait CashierReferralCodeUnit
{
    protected $orderReferralCodeDiscount;

    protected $orderReferralCodeDiscountType;

    /**
     * @return mixed
     */
    public function getOrderReferralCodeDiscount()
    {
        return $this->orderReferralCodeDiscount;
    }

    /**
     * @param $orderReferralCodeDiscount
     * @return CashierReferralCodeUnit
     */
    public function setOrderReferralCodeDiscount($orderReferralCodeDiscount)
    {
        $this->orderReferralCodeDiscount = $orderReferralCodeDiscount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderReferralCodeDiscountType()
    {
        return $this->orderReferralCodeDiscountType;
    }

    public function setOrderReferralCodeDiscountType($orderReferralCodeDiscountType)
    {
        $this->orderReferralCodeDiscountType = $orderReferralCodeDiscountType;

        return $this;
    }
}
