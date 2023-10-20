<?php

namespace App\Console\Commands;

use App\Models\Ordermanagercomment;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\GiftGiven2;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Traits\UserCommon;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class GiveSubscription extends Command
{
    use UserCommon;

    public const BASE_DATE = '2020-12-14 20:40:00';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:fixingSubscriptions {action : Action}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixing subscriptions';

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
        $action = $this->argument('action');

        if ($action == 'giveGiftSubscriptions') {
            $this->giveGiftSubscriptions();
            return 0;
        }

        if ($action = 'extendSubscriptions') {
            $this->extendSubscriptions();
            return 0;
        }

        $this->error('Invalid action');
        return 0;
    }

    private function giveGiftSubscriptions()
    {
        $product = Product::find(Product::SUBSCRIPTION_1_MONTH);
        $users = User::query()->whereHas('subscribedProducts', function ($q) use ($product) {
            $q->where('subscription_id', $product->id)
                ->where('valid_until', '<', self::BASE_DATE);
        })->get();

        $count = $users->count();

        if ($count == 0) {
            $this->warn('No subscription found');
            return null;
        }

        if (!$this->confirm("$count subscriptions found, do you wish to continue?", true)) {
            return null;
        }

        $bar = $this->output->createProgressBar($count);
        /** @var User $user */
        foreach ($users as $user) {
            $userFullName = $this->getUserFullName($user);
            $orderId = $this->createSubscriptionOrder($user->id, Product::SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY);
            if (!$orderId) {
                continue;
            }

            /** @var Subscription $unfinishedSubscription */
            $unfinishedSubscription = $user->subscribedProducts()
                ->where('subscription_id', $product->id)
                ->where('valid_until', '>=', self::BASE_DATE)->get()->first();

            if (isset($unfinishedSubscription)) {
                try {
                    $user->subscribedProducts()->attach(Product::SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY, [
                        'order_id' => $orderId,
                        'values' => null,
                        'valid_since' => $unfinishedSubscription->valid_until,
                        'valid_until' => Carbon::parse($unfinishedSubscription->valid_until)->addMonth(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                } catch (QueryException $e) {
                    Log::channel('fixingSubscriptions')->info('Database error on adding gift subscription to user who had unfinished subscription: '.$user->id);
                }
                $user->notify(new GiftGiven2($userFullName, 'وفاداران', 'یک ماه استفاده از زمانکوب',
                    'به همین دلیل یک ماه به اشتراک زمانکوب شما افزوده شد'));
                $bar->advance();
                continue;
            }

            try {
                $user->subscribedProducts()->attach(Product::SUBSCRIPTION_1_MONTH_TIMEPOINT_ONLY, [
                    'order_id' => $orderId,
                    'values' => null,
                    'valid_since' => Carbon::now('Asia/Tehran'),
                    'valid_until' => Carbon::now('Asia/Tehran')->addMonth(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                $user->notify(new GiftGiven2($userFullName, 'وفاداران', 'یک ماه استفاده از زمانکوب',
                    'از هم اکنون می توانید هنگام تماشای فیلمهای آلاء از قابلیت زمانکوب استفاده کنید'));
            } catch (QueryException $e) {
                Log::channel('fixingSubscriptions')->info('Database error on adding gift subscription to user: '.$user->id);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info('Done!');
    }

    private function createSubscriptionOrder(int $userId, int $productId): int
    {
        try {
            $order = OrderRepo::createBasicCompletedOrder($userId, config('constants.PAYMENT_STATUS_PAID'), 0, 0, null,
                0, 1);

            OrderproductRepo::createGiftOrderproduct($order->id, $productId, 0);

            Ordermanagercomment::create([
                'user_id' => 1,
                'order_id' => $order->id,
                'comment' => 'ثبت سیستمی . به دلیل اشتباه فنی ، اشتراک خریداری شده توسط ایشان به جای یک ماه 28 روز بود . برای جبران این سفارش به عنوان هدیه به ایشان اهدا شد.'
            ]);

            return $order->id;

        } catch (Exception $e) {
            $order->delete();
            Log::channel('fixingSubscriptions')->info('Error on creating subscription order for user: '.$userId);
            Log::channel('fixingSubscriptions')->error('file:'.$e->getFile().':'.$e->getLine());
        }

        return 0;
    }

    private function extendSubscriptions()
    {
        $productIds = [Product::SUBSCRIPTION_12_MONTH, Product::SUBSCRIPTION_3_MONTH, Product::SUBSCRIPTION_1_MONTH];

        foreach ($productIds as $productId) {
            $this->info("Processing product $productId");
            $subscriptions = Subscription::query()->where('subscription_id', $productId)
                ->where('valid_until', '>=', self::BASE_DATE);

            if ($productId == Product::SUBSCRIPTION_1_MONTH) {
                $subscriptions->where('id', '<=', 5047);
                $days = 3;
            } elseif ($productId == Product::SUBSCRIPTION_3_MONTH) {
                $subscriptions->where('id', '<=', 5048);
                $days = 6;
            } elseif ($productId == Product::SUBSCRIPTION_12_MONTH) {
                $subscriptions->where('id', '<=', 5024);
                $days = 29;
            } else {
                $this->warn('productId not valid!');
                continue;
            }

            $subscriptions = $subscriptions->get();
            $count = $subscriptions->count();
            if (!$this->confirm("$count subscriptions found for product $productId, do you wish to continue?", true)) {
                return null;
            }

            $bar = $this->output->createProgressBar($count);
            /** @var Subscription $subscription */
            foreach ($subscriptions as $subscription) {
                $validUntil = Carbon::parse($subscription->valid_until);
                try {
                    $subscription->update([
                        'valid_until' => $validUntil->addDays($days)
                    ]);
                } catch (Exception $e) {
                    Log::channel('extendSubscriptions')->info('Database error on updating subscription: '.$subscription->id);
                }

                $bar->advance();
            }

            $bar->finish();
        }
    }
}
