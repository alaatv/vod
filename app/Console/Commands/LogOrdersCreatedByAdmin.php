<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Loging\ActivityLogRepo;
use Illuminate\Console\Command;

class LogOrdersCreatedByAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:LogOrdersCreatedByAdmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find orders that added by admins and log them before deleting insertor_id column';

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
     * @return int
     */
    public function handle()
    {
        $orders = Order::whereNotNull('insertor_id')->get();

        $bar = $this->output->createProgressBar($orders->count());
        if (!$this->confirm("{$orders->count()} orders find, do you wand to continue? ", true)) {
            return 0;
        }
        $bar->start();
        foreach ($orders as $order) {
            $causer = User::find($order->insertor_id)->first();

            // separate products that added as no gift
            $orderProducts = $order->orderproducts->filter(function ($item) {
                return !$item->isGiftType();
            })->pluck('product_id')->toArray();

            // separate products that added as gift
            $orderGifts = $order->orderproducts->filter(function ($item) {
                return $item->isGiftType();
            })->pluck('product_id')->toArray();

            ActivityLogRepo::LogItemsAddedToOrder($causer, $order, $order->user, $orderGifts, $orderProducts);


            $order->update(['insertor_id' => null]);
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
