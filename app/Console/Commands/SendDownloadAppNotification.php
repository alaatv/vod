<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class SendDownloadAppNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:sendDownloadAppNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send SMS to users including android app url';

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
     */
    public function handle()
    {
        $users = User::whereHas('orders', function ($q) {
            $q->whereHas('orderproducts', function ($q2) {
                $q2->where('product_id', '<>', Product::CUSTOM_DONATE_PRODUCT);
            })->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                ->whereIn('paymentstatus_id', Order::getDoneOrderPaymentStatus())
                ->where('completed_at', '>=', '2019-03-21 00:00:00')
                ->where('completed_at', '<=', '2020-05-04 00:00:00');
        })->orderBy('id')
            ->get();

        $usersCount = $users->count();

        if (!$this->confirm("$usersCount found . Would you like to continue?", true)) {
            $this->info('Done!');
            return 0;
        }
        $sendCounter = (int) ($usersCount / 100);
        $bar = $this->output->createProgressBar($sendCounter);
        for ($i = 0; $i <= $sendCounter; $i++) {
            $skip = $i * 100;
            $delay = $i * 10;
            $partialUsers = $users->skip($skip)->take(100);
            if ($delay > 0) {
                dispatch(new \App\Jobs\SendDownloadAppNotification($partialUsers))->delay(now()->addMinutes($delay));
            } else {
                dispatch(new \App\Jobs\SendDownloadAppNotification($partialUsers));
            }

            $this->info(($i + 1).' set of users dispatched.');
            $bar->advance();
        }
        $bar->finish();

        $this->info('Done!');
    }
}
