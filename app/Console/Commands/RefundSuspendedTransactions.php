<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
use Illuminate\Console\Command;

class RefundSuspendedTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:refundTransaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refunds suspended transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        $walletTransactions = Transaction::where('paymentmethod_id', config('constants.PAYMENT_METHOD_WALLET'))
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUSPENDED'))
            ->get();

        $totalTransactionCount = $walletTransactions->count();
        $this->info('total transactions: '.$totalTransactionCount);
        $this->info("\n\n");
        $this->info('total transactions cost: '.number_format($walletTransactions->sum('cost')).' Tomans');
        $this->info("\n\n");
        $totalRefund = 0;
        $bar = $this->output->createProgressBar($totalTransactionCount);
        /** @var Transaction $walletTransaction */

        $depositFlag = false;
        if ($this->confirm('Do you want to refund these transactions? type No if you only want to analyze them',
            true)) {
            $depositFlag = true;
        }

        foreach ($walletTransactions as $walletTransaction) {
            /** @var Order $order */
            $order = $walletTransaction->order;
            /** @var Wallet $wallet */
            $wallet = $walletTransaction->wallet;
            if (!isset($order)) {
                $this->info('Transaction does not have order: '.$walletTransaction->id);
                $this->info("\n\n");
                continue;
            }

            if (!isset($wallet)) {
                $this->info('Transaction does not have wallet: '.$walletTransaction->id);
                $this->info("\n\n");
                continue;
            }

            if ($walletTransaction->cost < 0) {
                $this->info('Transaction cost is minus: '.$walletTransaction->id);
                $this->info("\n\n");
                continue;
            }

            if ($walletTransaction->cost == 0) {
                $this->info('Transaction cost is zero: '.$walletTransaction->id);
                $this->info("\n\n");
                continue;
            }

            if ($order->paymentstatus_id == config('constants.PAYMENT_STATUS_PAID')) {
                $this->info('Order status is paid: '.$walletTransaction->id);
                $this->info("\n\n");
                continue;
            }

            if ($order->orderstatus_id != config('constants.ORDER_STATUS_CLOSED') && $order->orderstatus_id != config('constants.ORDER_STATUS_CANCELED')) {
                $this->info('Order status is not closed: '.$order->orderstatus_id.' '.$walletTransaction->id);
                $this->info("\n\n");
                continue;
            }

            if (!$depositFlag) {

                $bar->advance();
                continue;
            }
            $result = $walletTransaction->depositThisWalletTransaction();
            if ($result['result']) {
                $totalRefund += $walletTransaction->cost;
                $walletTransaction->delete();
            } else {
                $this->info('Could not update wallet '.$wallet->id.' : '.$walletTransaction->id);
                $this->info("\n\n");
            }


            $bar->advance();
        }

        $bar->finish();

        $this->info("\n\n");
        $this->info('total refunded : '.number_format($totalRefund));
    }
}
