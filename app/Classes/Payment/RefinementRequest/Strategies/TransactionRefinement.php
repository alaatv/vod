<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 1/8/2019
 * Time: 1:29 PM
 */

namespace App\Classes\Payment\RefinementRequest\Strategies;

use App\Classes\Payment\RefinementRequest\Refinement;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Response;

class TransactionRefinement extends Refinement
{
    /**
     * @return Refinement
     */
    public function loadData(): Refinement
    {
        if ($this->statusCode != Response::HTTP_OK) {
            return $this;
        }

        $transaction = $this->getTransaction();
        if ($transaction === false) {
            $this->statusCode = Response::HTTP_NOT_FOUND;
            $this->message = 'تراکنشی یافت نشد.';

            return $this;
        }

        $this->transaction = $transaction;
        $order = $this->getOrder();

        if ($order === false) {
            $this->statusCode = Response::HTTP_NOT_FOUND;
            $this->message = 'سفارش یافت نشد.';

            return $this;
        }

        $this->order = $order;
        $this->orderUniqueId = $order->id.Carbon::now()->timestamp;
        $this->user = $this->order->user;
        $this->cost = $this->transaction->cost;
//        if ($this->canDeductFromWallet()) {
//            $this->payByWallet();
//        }

        $this->resetWalletPendingCredit();
        $this->statusCode = Response::HTTP_OK;
        $this->description .= $this->getDescription();

        return $this;
    }

    private function getTransaction(): Transaction
    {
        return Transaction::find($this->inputData['transaction_id']);
    }

    /**
     * @return Order|bool
     */
    private function getOrder()
    {
        $transaction = $this->transaction;
        if (!isset($transaction)) {
            return false;
        }

        $order = $transaction->order->load(['transactions', 'coupon']);
        if (!isset($order)) {
            return false;
        }

        return $order;
    }

    /**
     * @return string
     */
    private function getDescription(): string
    {
        $description = '';
        if (isset($this->inputData['transaction_id'])) {
            $description = 'پرداخت قسط -';
        }

        return $description;
    }
}
