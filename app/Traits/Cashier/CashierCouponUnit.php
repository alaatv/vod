<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/27/2018
 * Time: 11:38 AM
 */

namespace App\Traits\Cashier;

trait CashierCouponUnit
{
    protected $orderCoupon;

    protected $orderCouponDiscountCostAmount;

    protected $orderCouponDiscountPercentage;

    protected $totalRawPriceWhichDoesntHaveDiscount;

    protected $totalRawPriceWhichHasDiscount;

    protected $totalPriceWithDiscount; //It is totalRawPriceWhichHasDiscount after calculating it's discount

    protected $temporaryTotalPriceWithDiscount;

    /**
     * @return mixed
     */
    public function getOrderCoupon()
    {
        return $this->orderCoupon;
    }

    /**
     * @param  mixed  $orderCoupon
     *
     * @return mixed
     */
    public function setOrderCoupon($orderCoupon)
    {
        $this->orderCoupon = $orderCoupon;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderCouponDiscountCostAmount()
    {
        return $this->orderCouponDiscountCostAmount;
    }

    /**
     * @param  mixed  $orderCouponDiscountCostAmount
     *
     * @return mixed
     */
    public function setOrderCouponDiscountCostAmount($orderCouponDiscountCostAmount)
    {
        $this->orderCouponDiscountCostAmount = $orderCouponDiscountCostAmount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderCouponDiscountPercentage()
    {
        return $this->orderCouponDiscountPercentage;
    }

    /**
     * @param  mixed  $orderCouponDiscountPercentage
     *
     * @return mixed
     */
    public function setOrderCouponDiscountPercentage($orderCouponDiscountPercentage)
    {
        $this->orderCouponDiscountPercentage = $orderCouponDiscountPercentage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalRawPriceWhichDoesntHaveDiscount()
    {
        return $this->totalRawPriceWhichDoesntHaveDiscount;
    }

    /**
     * @param  mixed  $totalRawPriceWhichDoesntHaveDiscount
     *
     * @return mixed
     */
    public function setTotalRawPriceWhichDoesntHaveDiscount($totalRawPriceWhichDoesntHaveDiscount)
    {
        $this->totalRawPriceWhichDoesntHaveDiscount = $totalRawPriceWhichDoesntHaveDiscount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalRawPriceWhichHasDiscount()
    {
        return $this->totalRawPriceWhichHasDiscount;
    }

    /**
     * @param  mixed  $totalRawPriceWhichHasDiscount
     *
     * @return mixed
     */
    public function setTotalRawPriceWhichHasDiscount($totalRawPriceWhichHasDiscount)
    {
        $this->totalRawPriceWhichHasDiscount = $totalRawPriceWhichHasDiscount;
        $this->temporaryTotalPriceWithDiscount = $totalRawPriceWhichHasDiscount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalPriceWithDiscount()
    {
        return $this->totalPriceWithDiscount;
    }

    /**
     * @param  mixed  $totalPriceWithDiscount
     *
     * @return mixed
     */
    public function setTotalPriceWithDiscount($totalPriceWithDiscount)
    {
        $this->totalPriceWithDiscount = $totalPriceWithDiscount;
        $this->temporaryTotalPriceWithDiscount = $totalPriceWithDiscount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemporaryTotalPriceWithDiscount()
    {
        return $this->temporaryTotalPriceWithDiscount;
    }

    /**
     * @param  mixed  $temporaryTotalPriceWithDiscount
     *
     * @return mixed
     */
    public function setTemporaryTotalPriceWithDiscount($temporaryTotalPriceWithDiscount)
    {
        $this->temporaryTotalPriceWithDiscount = $temporaryTotalPriceWithDiscount;

        return $this;
    }
}
