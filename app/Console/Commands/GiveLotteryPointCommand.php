<?php

namespace App\Console\Commands;

use App\Models\Bon;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\ReferralCode;

use App\Models\Userbon;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GiveLotteryPointCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:giveLotteryPoint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $bonName;
    private ?Collection $usersBons = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->bonName = config('constants.BON2');
        $this->usersBons = collect();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->calcBeforeYaldaPoints();
        $this->calcInYaldaPoints();
        $this->callReferralPoints();
        $this->setPointsToUsers();

        return 0;
    }

    private function calcBeforeYaldaPoints()
    {
        $this->info('Giving Points of before Yalda ...');
        $orderProducts = Orderproduct::with(['order'])
            ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->where('cost', '<>', 0)
            ->whereIn('product_id', Product::ALL_PACK_ABRISHAM_PRODUCTS)
            ->whereHas('order', function ($query) {
                $query->where('completed_at', '>=', '2020-12-21 00:00:00')
                    ->where('completed_at', '<', config('constants.EVENTS.BEGIN'))
                    ->whereIn('paymentstatus_id',
                        [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
                    ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'));
            })->get();

        $bar = $this->output->createProgressBar($orderProducts->count());
        $this->info("\n");
        foreach ($orderProducts as $op) {
            $item = $this->makeNewUserBone($op->order->user_id, 1);
            $this->pushToUserBons($item);
            $bar->advance();
        }
        $bar->finish();
        $this->info("\n");
    }

    private function makeNewUserBone(int $userId, int $points)
    {
        return ['userId' => $userId, 'points' => $points];
    }

    private function pushToUserBons(array $data)
    {
        $userId = Arr::get($data, 'userId');
        $newPoint = Arr::get($data, 'point');

        $existingRecords = $this->usersBons->where('userId', $userId);

        if ($existingRecords->isEmpty()) {
            $this->usersBons->push($data);
            return null;
        }

        foreach ($existingRecords as $key => $existingRecord) {
            $newPoint += $existingRecord['points'];
            $existingRecords->put($key, ['userId' => $userId, 'points' => $newPoint]);
        }

        return null;
    }

    private function calcInYaldaPoints()
    {
        $this->info('Giving Points of in Yalda ...');
        $orderProducts = Orderproduct::with(['order'])
            ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->whereIn('product_id', array_keys(Product::ALL_ABRISHAM_PRODUCTS))
            ->where('cost', '<>', 0)
            ->whereHas('order', function ($query) {
                $query->where('completed_at', '>=', config('constants.EVENTS.BEGIN'))
                    ->where('completed_at', '<', '2022-01-31 00:00:00')
                    ->whereIn('paymentstatus_id',
                        [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
                    ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'));
            })->get();

        $bar = $this->output->createProgressBar($orderProducts->count());
        $this->info("\n");
        foreach ($orderProducts as $op) {
//            $points = $this->calcPoints($op);
            $item = $this->makeNewUserBone($op->order->user_id, 1);
            $this->pushToUserBons($item);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");
    }

    private function callReferralPoints()
    {
        $this->info('Giving Points of referal codes ...');
        $referralCodes = ReferralCode::with(['users'])->whereHas('users')->get();

        $bar = $this->output->createProgressBar($referralCodes->count());
        $this->info("\n");

        /** @var ReferralCode $referralCode */
        foreach ($referralCodes as $referralCode) {
            foreach ($referralCode->users as $user) {
                $order = Order::query()
                    ->where('id', $user->subject_id)
                    ->where('completed_at', '>=', config('constants.EVENTS.BEGIN'))
                    ->where('completed_at', '<', '2022-01-31 00:00:00')
                    ->whereIn('paymentstatus_id',
                        [config('constants.PAYMENT_STATUS_PAID'), config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')])
                    ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED'))->first();

                if (!isset($order)) {
                    continue;
                }

                $orderProducts = $order->orderproducts
                    ->where('orderproducttype_id', config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                    ->where('cost', '<>', 0)
                    ->whereIn('product_id', array_keys(Product::ALL_ABRISHAM_PRODUCTS));

                foreach ($orderProducts as $op) {
//                    $points = $this->calcPoints($op);
                    $item = $this->makeNewUserBone($referralCode->owner_id, 1);
                    $this->pushToUserBons($item);
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");
    }

    private function setPointsToUsers()
    {
        $failedCounter = 0;
        $successCounter = 0;
        $maxPoint = 0;
        $maxPointUserId = '';

        if (!$this->confirm('Points are ready, Do you wish to store them?')) {
            return 0;
        }
        $bar = $this->output->createProgressBar($this->usersBons->count());

        $bar->start();
        $this->info("\n");

        foreach ($this->usersBons as $usersBon) {
            $userId = Arr::get($usersBon, 'userId');
            $points = Arr::get($usersBon, 'points', 0);

            if (!isset($userId)) {
                $failedCounter++;
                $this->info("\n");
                $this->info('No user found  for userbon');
                Log::channel('giveLotteryPointsErrors')->error('No user found  for userbon');
                $bar->advance();
                continue;
            }

            if ($points == 0) {
                $bar->advance();
                continue;
            }

            try {
                $userBon = Userbon::create([
                    'bon_id' => Bon::ALAA_POINT_BON,
                    'user_id' => $userId,
                    'totalNumber' => $points,
                    'usedNubmer' => 0,
                    'userbonstatus_id' => config('constants.USERBON_STATUS_ACTIVE'),
                ]);
            } catch (QueryException $e) {
                $failedCounter++;
                Log::channel('giveLotteryPointsErrors')->error("Error on inserting points for user $userId");
                $bar->advance();
                continue;
            }

            $user = $userBon->user;
            Log::channel('giveLotteryPointsInfo')->info("$points points were given to user $userId {$user->mobile}");
            $successCounter++;

            if ($points > $maxPoint) {
                $maxPoint = $points;
                $maxPointUserId = $userId;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");

        $this->info('Number of successfully processed users: '.$successCounter);
        $this->info('Number of failed users: '.$failedCounter);
        $this->info('Max given points:'.$maxPoint.', was given to user: '.$maxPointUserId);
        $this->info('DONE!');
        $this->info('Please check these logs:');
        $this->info('giveLotteryPointsErrors : for errors');
        $this->info('giveLotteryPointsWarnings : for warnings');
        $this->info('giveLotteryPointsInfo : for the list of given points');
    }

    private function calcPoints(Orderproduct $op): int
    {
        if (in_array($op->product_id, Product::ALL_SINGLE_ABRISHAM_PRODUCTS)) {
            return 1;
        }
        if ($op->product_id === Product::RAHE_ABRISHAM1401_PACK_OMOOMI) {
            return count(Product::ALL_ABRISHAM_PRODUCTS_OMOOMI);
        }
        if ($op->product_id === Product::RAHE_ABRISHAM99_PACK_RIYAZI) {
            return count(Product::ALL_ABRISHAM_PRODUCTS_EKHTESASI_RIYAZI);
        }
        if ($op->product_id === Product::RAHE_ABRISHAM99_PACK_TAJROBI) {
            return count(Product::ALL_ABRISHAM_PRODUCTS_EKHTESASI_TAJROBI);
        }

        return 0;
    }
}
