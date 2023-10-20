<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ServiceGiven;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GiveGiftServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:giveGift:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $q->where('completed_at', '>=', '2020-09-05 00:00:00')
                ->where('completed_at', '<', '2020-09-14 18:00:00')
                ->where('orderstatus_id', 2)->where('paymentstatus_id', 3)
                ->whereHas('orderproducts', function ($q2) {
                    $q2->whereIn('product_id', [
//                        Product::RAHE_ABRISHAM99_FIZIK_RIYAZI ,
//                        Product::RAHE_ABRISHAM99_FIZIK_TAJROBI,
//                        Product::RAHE_ABRISHAM99_PACK_RIYAZI ,
//                        Product::RAHE_ABRISHAM99_PACK_TAJROBI,
//                        Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI ,
//                        Product::RAHE_ABRISHAM99_SHIMI ,
//                        Product::RAHE_ABRISHAM99_ZIST ,
                    ]);
                });
        })->get();

        $usersCount = $users->count();

        $timepointCoupon = Coupon::find(8527);
        $usageNumber = $timepointCoupon->usageNumber;
        if (!$this->confirm("$usersCount users found. Do you wish to continue?", true)) {

            Artisan::call('cache:clear');
            $this->info('Done');

            return 0;
        }

        $bar = $this->output->createProgressBar($usersCount);

        /** @var User $user */
        foreach ($users as $user) {
            $subscriptionProduct = $user->subscribedProducts;
            if ($subscriptionProduct->isNotEmpty()) {
                $this->info('User had subscription: '.$user->id);
                Log::channel('giveGiftService')->info('User had subscription: '.$user->id);
                $this->info("\n");
                continue;
            }

            $newOrder = OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_PAID'), 0, 0,
                $timepointCoupon->id, $timepointCoupon->discount, 1);

            if (!isset($newOrder)) {
                $this->error('Error on creating order for user: '.$user->id);
                Log::channel('giveGiftService')->info('Error on creating order for user: '.$user->id);
                $this->info("\n");
                continue;
            }

            $newOrder->discount = 0;
            $newOrder->update([
                'cost' => 0,
                'costwithoutcoupon' => 0,
            ]);

            OrderproductRepo::createBasicOrderproduct($newOrder->id, Product::SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY, 0,
                0);
            $validUntil = Carbon::now('Asia/Tehran')->addWeeks(4);

            $user->subscribedProducts()->attach(Product::SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY, [
                'order_id' => $newOrder->id,
                'valid_since' => Carbon::now('Asia/Tehran'),
                'valid_until' => $validUntil,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $user->notify(new ServiceGiven('زمان کوب'));

            $usageNumber++;
            $bar->advance();
        }

        $timepointCoupon->update(['usageNumber' => $usageNumber]);
        $bar->finish();


        Artisan::call('cache:clear');
        $this->info('Done');

        return 0;
    }
}
