<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2019-03-14
 * Time: 13:27
 */

namespace App\Traits;



use App\Models\Bon;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

trait HandleOrderPayment
{
    /**
     * @param $order
     *
     * @return array
     */
    protected function handleOrderSuccessPayment(Order $order): void
    {
        $order->closeWalletPendingTransactions();

        $wallets = optional($order->user)->wallets;
        if (isset($wallets)) {
            $this->withdrawWalletPendings($order->id, $wallets);
        }

        Cache::tags('order_'.$order->id)->flush();
        $order = Order::Find($order->id);

        $updateOrderPaymentStatusResult = $this->updateOrderPaymentStatus($order);

        /** Attaching user bons for this order */
//        if ($updateOrderPaymentStatusResult['paymentstatus_id'] == config('constants.PAYMENT_STATUS_PAID')) {
//            $this->givesOrderBonsToUser($order);
//        }
    }

    /**
     * @param  Order  $order
     *
     * @return array
     */
    protected function updateOrderPaymentStatus(Order $order): array
    {
        if ($order->totalPaidCost() < $order->totalCost()) {
            $paymentstatus_id = $order->paymentstatus_id;
            if ($order->paymentstatus_id != config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')) {
                $paymentstatus_id = config('constants.PAYMENT_STATUS_INDEBTED');
            }
        } else {
            $paymentstatus_id = config('constants.PAYMENT_STATUS_PAID');
        }

        $result['paymentstatus_id'] = $paymentstatus_id;

//        uncomment if you don't close order before redirecting to gateway
        if (in_array($order->orderstatus_id, Order::OPEN_ORDER_STATUSES)) {
            $order->close();
        }

        $order->paymentstatus_id = $paymentstatus_id;
        $order->update([
            'paymentstatus_id' => $paymentstatus_id
        ]);

        return $result;
    }

    /**
     * @param  Order  $order
     *
     * @return array
     */
    private function givesOrderBonsToUser(Order $order): void
    {
        $bonName = config('constants.BON1');
        $bon = Bon::ofName($bonName)
            ->first();

        if (!isset($bon)) {
            return;
        }

        [$givenBonNumber, $failedBonNumber] = $order->giveUserBons($bonName);

    }
}
