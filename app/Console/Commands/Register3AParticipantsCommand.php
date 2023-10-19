<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Traits\APIRequestCommon;
use App\Traits\OrderCommon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

class Register3AParticipantsCommand extends Command
{
    use APIRequestCommon;
    use OrderCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:3A:registerParticipants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registering participants to 3A';

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
        $orders = Order::where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))->where('paymentstatus_id',
            config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'))
            ->whereHas('orderproducts', function ($q) {
                $q->whereIn('product_id', [
                ]);
            })
            ->get();


        if (!$this->confirm($orders->count().' 3a orders found , Do you wish to continue?')) {
            return 0;
        }

        $progress = new ProgressBar($this->output);
        foreach ($orders as $order) {
            $user = $order->user;
            if (!isset($user)) {
                Log::channel('debug')->error('Register3AParticipantsCommand => Order has no user :'.$order->id);
                $progress->advance();
                continue;
            }

//            $this->sendOrderNotifications( $order  , $user );
//            $user->notify(new _3aExamNotification());
            $progress->advance();
        }

        $progress->finish();
    }
}
