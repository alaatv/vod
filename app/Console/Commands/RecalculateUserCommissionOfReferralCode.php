<?php

namespace App\Console\Commands;

use App\Jobs\CalculateBilling;
use App\Jobs\CalculateUserCommission;
use App\Models\Order;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RecalculateUserCommissionOfReferralCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:referral-commission {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command recalculates users commissions of referral codes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $userId = $this->argument('user');
        $orders = Order::where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
            ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'))
            ->whereHas('referralCode', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->get();
        $bar = $this->output->createProgressBar(count($orders));
        $bar->start();
        foreach ($orders as $order) {
            dispatch(new CalculateBilling([$order->id]))->onConnection('sync');
            dispatch(new CalculateUserCommission($order))->onConnection('sync');
            $bar->advance();
        }
        $bar->finish();
        return CommandAlias::SUCCESS;
    }
}
