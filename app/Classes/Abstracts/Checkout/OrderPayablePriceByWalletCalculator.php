<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 11/26/2018
 * Time: 5:56 PM
 */

namespace App\Classes\Abstracts\Checkout;

use App\Models\Order;
use PHPUnit\Framework\Exception;

abstract class OrderPayablePriceByWalletCalculator extends CheckoutProcessor
{
    public function process(Cashier $cashier)
    {
        $order = $cashier->getOrder();
        $finalPrice = $cashier->getFinalPrice();
        if (!isset($order)) {
            throw new Exception('Order has not been set');
        }

        if (!isset($finalPrice)) {
            throw new Exception('Final price has not been set');
        }

        $payableAmountByWallet = $this->calculateAmountPaidByWallet($order, $finalPrice);

        $cashier->setPayableAmountByWallet($payableAmountByWallet);

        return $this->next($cashier);
    }

    /**
     * Calculates the sum price for passed Orderproduct collection
     *
     * @param  Order  $order
     * @param         $finalPrice
     *
     * @return int
     */
    abstract protected function calculateAmountPaidByWallet(Order $order, $finalPrice): int;
}
