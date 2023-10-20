<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/27/2018
 * Time: 11:39 AM
 */

namespace App\Traits\Cashier;

trait CashierWalletUnit
{
    protected $payableAmountByWallet;

    /**
     * @return mixed
     */
    public function getPayableAmountByWallet()
    {
        return $this->payableAmountByWallet;
    }

    /**
     * @param  mixed  $payableAmountByWallet
     *
     * @return mixed
     */
    public function setPayableAmountByWallet($payableAmountByWallet)
    {
        $this->payableAmountByWallet = $payableAmountByWallet;

        return $this;
    }
}
