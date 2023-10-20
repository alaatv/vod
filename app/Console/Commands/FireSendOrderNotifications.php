<?php

namespace App\Console\Commands;

use App\Events\SendOrderNotificationsEvent;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FireSendOrderNotifications extends Command
{
    protected $orders;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:fire:sendOrderNotifications {--from=} {--to=}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fire SendOrderNotifications Event on all closed orders in a specific period';
    private $startDate;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->orders = Order::whereOrderstatusId(2)
            ->whereIn('paymentstatus_id', [
                config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
                config('constants.PAYMENT_STATUS_PAID'),
                config('constants.PAYMENT_STATUS_INDEBTED'),
            ]);
        $this->startDate = Carbon::now()->subHours(6)->toDateTimeString();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->getDefinition()->getOption('from')->setDefault($this->startDate);
        $validOrders = $this->setDatesQueryOnOrdersCollection($this->orders);

        $count = $validOrders->count();
        if (!$this->confirm("{$count} found, continue?")) {
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        foreach ($validOrders as $order) {
            event(new SendOrderNotificationsEvent($order, $order->user));
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done!');
        return 0;
    }

    public function setDatesQueryOnOrdersCollection($orders)
    {
        $startDate = $this->option('from');
        $endDate = $this->option('to') ? $this->option('to') : Carbon::now()->toDateTimeString();
        return $orders->where('completed_at', '>=', $startDate)->where('completed_at', '<=', $endDate)->get();
    }
}
