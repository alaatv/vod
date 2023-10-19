<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/27/2018
 * Time: 11:37 AM
 */

namespace App\Traits\Cashier;



use App\Models\Order;

trait CashierOrderUnit
{
    protected $order;

    protected $totalPrice; // Total price before calculating Order's discount

    protected $temporaryFinalPrice;

    protected $finalPrice;

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param  mixed  $order
     *
     * @return mixed
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param  mixed  $totalPrice
     *
     * @return mixed
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
        $this->temporaryFinalPrice = $totalPrice;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemporaryFinalPrice()
    {
        return $this->temporaryFinalPrice;
    }

    /**
     * @param  mixed  $temporaryFinalPrice
     *
     * @return mixed
     */
    public function setTemporaryFinalPrice($temporaryFinalPrice)
    {
        $this->temporaryFinalPrice = $temporaryFinalPrice;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }

    /**
     * @param  mixed  $finalPrice
     *
     * @return mixed
     */
    public function setFinalPrice($finalPrice)
    {
        $this->finalPrice = $finalPrice;
        $this->temporaryFinalPrice = $finalPrice;

        return $this;
    }
}
