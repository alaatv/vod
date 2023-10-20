<?php

namespace App\Console\Commands;

use App\Events\SendOrderNotificationsEvent;
use App\Models\Coupon;
use App\Repositories\OrderRepo;
use App\Traits\DateTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessOnOrdersCommand extends Command
{
    use DateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'alaaTv:processOnOrders
        {action : Choosing the action : sendNotificationForPaidClosedOrders, closeUnpaidOrders}
        {pay_status : Order payment statuses id}
        {--from= : Apply precess from this date}
        {--to= : Apply precess to this date}
        {--coupon= : Coupon code}
        {--products= : Products id}
        {--order_status= : Order statuses id}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply some process on some orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $action = $this->argument('action');
        $paymentStatusIds = $this->argument('pay_status');
        $from = $this->option('from');
        $to = $this->option('to');
        $couponCode = $this->option('coupon');
        $productIds = $this->option('products');
        $orderStatusIds = $this->option('order_status');

        if (!empty($from) && Carbon::createFromFormat('Y-m-d H:i:s', $from) === false) {
            $this->error("From date isn't valid!");
            return false;
        }

        if (!empty($to) && Carbon::createFromFormat('Y-m-d H:i:s', $to) === false) {
            $this->error("To date isn't valid!");
            return false;
        }

        $coupon = Coupon::where('code', $couponCode)->first();
        $paymentStatusIds = explode(',', $paymentStatusIds);
        $productIds = explode(',', $productIds);
        $orderStatusIds = explode(',', $orderStatusIds);

        switch ($action) {
            case 'sendNotificationForPaidClosedOrders':
                $this->sendOrderNotificationsEventOnPaidClosedCompletedOrders($from, $to, $coupon);
                break;
            case 'closeUnpaidOrders':
                $this->closeOldOrders($paymentStatusIds, $productIds, $from, $to, $coupon, $orderStatusIds);
                break;
            default:
                $this->info("The '{$action}' process not found!");
                break;
        }

        $this->info('Done!');
        return true;
    }

    /**
     * @param  string  $from
     * @param  string|null  $to
     * @param  Coupon|null  $coupon
     * @return void
     */
    private function sendOrderNotificationsEventOnPaidClosedCompletedOrders(
        string $from,
        string $to = null,
        ?Coupon $coupon = null
    ) {
        $orders = OrderRepo::generalOrderSelection($from, to: $to, coupon: $coupon)->get();

        $ordersCount = $orders->count();
        $msg = "{$ordersCount} orders found";
        if (!is_null($coupon)) {
            $msg .= " with coupon code '{$coupon->code}'";
        }
        $this->info("{$msg}.");

        if ($ordersCount <= 0 || !$this->output->confirm('Do you want to continue?')) {
            die;
        }

        $this->sendOrderNotificationsEventOnOrders($orders);
    }

    private function sendOrderNotificationsEventOnOrders($orders)
    {
        foreach ($orders as $order) {
            event(new SendOrderNotificationsEvent($order, $order->user, true));
        }
    }

    private function closeOldOrders(
        array $paymentStatusIds,
        ?array $productIds = null,
        ?string $from = null,
        ?string $to = null,
        ?Coupon $coupon = null,
        ?array $orderStatusIds = null
    ) {
        OrderRepo::generalOrderSelectionWithPayment($paymentStatusIds, $productIds, $from, $to, $coupon,
            $orderStatusIds)
            ->get()
            ->each
            ->update([
                'orderstatus_id' => config('constants.ORDER_STATUS_CANCELED'),
            ]);
    }
}
