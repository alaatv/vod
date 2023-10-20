<?php

namespace App\Jobs;

use App\Classes\Pricing\Alaa\AlaaInvoiceGenerator;
use App\Models\Transaction;
use App\Notifications\CouponRecycled;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CheckCouponOfUnpaidOrder implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $transaction;

    /**
     * Create a new job instance.
     *
     * @param  Transaction  $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $order = $this->transaction->order;
        if (!isset($order)) {
            return null;
        }

        Cache::tags('coupon_user_'.$order->user_id)->flush();

        if (!isset($order->coupon_id)) {
            return;
        }
        if ($order->orderstatus_id == config('constants.ORDER_STATUS_CLOSED') && $order->paymentstatus_id == config('constants.PAYMENT_STATUS_PAID')) {
            return null;
        }
        $transferedTransactions = $order->transactions()->where('id', '<>',
            $this->transaction->id)->where('transactionstatus_id',
            config('constants.TRANSACTION_STATUS_TRANSFERRED_TO_PAY'))->get();

        if ($transferedTransactions->isNotEmpty()) {
            return null;
        }
        $coupon = $order->coupon;
        if (isset($coupon)) {
            $order->detachCoupon();
            if ($order->updateWithoutTimestamp()) {
                $coupon->decreaseUseNumber()->update();

                (new AlaaInvoiceGenerator())->generateOrderInvoice($order);
                Cache::tags([
                    'order_'.$order->id,
                    'order_'.$order->id.'_coupon',
                ])->flush();

                $user = $order->user;
                if (isset($user)) {
                    Cache::tags([
                        'user_'.$user->id.'_closedOrders',
                    ])->flush();
                    $user->notify(new CouponRecycled($coupon));
                }
            }
        }

    }
}
