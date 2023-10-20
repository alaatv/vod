<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Traits\DateTrait;
use App\Traits\Helper;
use Illuminate\Console\Command;

class MarketingNotificationCommand extends Command
{
    use DateTrait;
    use Helper;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:marketing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending marketing notifications';

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
        $this->info('Disabled');
        return false;

        $users = User::distinct()->whereNotInBlackList()->whereHas('orderproducts', function ($q) {
            $q->whereIn('product_id', [1101, 1100, 1099, 1098, 1095, 1094, 1093, 1092, 1091, 1090,])
                ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                ->whereHas('order', function ($q2) {
                    $q2->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))
                        ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_PAID'));
                });
        })->pluck('mobile')->toArray();

//        $users = ['09194251469'];
        $count = count($users);
        if (!$this->confirm("$count users found, continue?")) {
            return false;
        }

        $this->info('sending the sms');
        // Sending buld SMS
        $numbers = array_chunk($users, 25000);
        foreach ($numbers as $number) {
            $paramsTaftanBiAbrisham = [
                'message' => 'فقط برای شما ...
کل دروس راه ابریشم 2 با 600 هزار تومان⚡
توی جشنواره شگفت انگیز آلا فقط تا امروز مهلت باقیست!
عدد "6" رو همینجا برامون بفرست تا راهنماییت کنیم 😊',
                'to' => $number,
            ];

            $this->medianaSendSMS($paramsTaftanBiAbrisham);
            $this->info('sent to first chunk');
        }


        $this->info('Done!');
    }
}
