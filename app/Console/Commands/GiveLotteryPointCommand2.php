<?php

namespace App\Console\Commands;

use App\Models\Bon;
use App\Models\Lottery;
use App\Models\LotteryStatus;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\User;
use App\Models\Userbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use src\Illuminate\Database\QueryException;

class GiveLotteryPointCommand2 extends Command
{
    public const SINCE_DATE = '2020-08-22 00:00:00';
    public const TILL_DATE = '2020-12-28 00:00:00';
    public const RAH_BALAD = 'rahBalad';
    public const RAH_ABRISHAM = 'raheAbrisham';


    protected $signature = 'alaaTv:giveLotteryPoint2 {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $bonName;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->bonName = config('constants.BON2');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $lottery = Lottery::find($this->argument('id'));
            $this->giveRaheAbrishamPoints();
            $lottery->lottery_status_id = LotteryStatus::HOLD;
            $lottery->save();
        } catch (Exception $exception) {
            $lottery->lottery_status_id = LotteryStatus::REPORT_SCORING_ERROR;
            $lottery->save();
            Log::channel('giveLotteryPointsErrors')
                ->warning(
                    'GivePointsError: lotteryId='.
                    $lottery->id.
                    ', lotteryName='.
                    $lottery->name.
                    ', errorFile='.
                    $exception->getFile().
                    ', errorLine='.
                    $exception->getLine().
                    ', errorMessage='.
                    $exception->getMessage()
                );
        }
    }

    public function giveRaheAbrishamPoints()
    {
        {
            $bon = Bon::where('name', $this->bonName)->first();

            $successCounter = 0;
            $failedCounter = 0;
            $maxPoint = 0;
            $maxPointUserId = '';
            $users = collect();

            $products = Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS;
            $orderproducts = Orderproduct::query()
                ->where('orderproducttype_id', '<>', config('constants.ORDER_PRODUCT_GIFT'))
                ->whereHas('order', function ($q) {
                    $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                        ->whereIn('paymentstatus_id', [
                            config('constants.PAYMENT_STATUS_PAID'),
                            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                        ])
                        ->whereBetween('completed_at', [self::SINCE_DATE, self::TILL_DATE])
                        ->whereDoesntHave('user', function ($q) {
                            $q->whereHas('roles', function ($q2) {
                                $q2->whereIn('name', [config('constants.ROLE_EMPLOYEE'), 'walletGiver', 'admin']);
                            });
                        });
                })->whereIn('product_id', $products)
                ->get();
            $orderproductsCount = $orderproducts->count();

            $pointsBar = $this->output->createProgressBar($orderproductsCount);
            foreach ($orderproducts as $orderproduct) {
                $cost = $orderproduct->price['final'];
                $user = $orderproduct->order->user;
                if (!isset($user)) {
                    Log::channel('giveLotteryPointsWarnings')->warning('Orderproduct '.$orderproduct->id.' has no owner user!');
                    continue;
                }

                $userRecords = $users->where('user_id', $user->id);
                if ($userRecords->isEmpty()) {
                    $users->push([
                        'user_id' => $user->id,
                        'totalAmount' => $cost,
                        'point' => 1,
                    ]);
                    continue;
                }

                foreach ($userRecords as $key => $userRecord) {
                    $userRecord['totalAmount'] = $userRecord['totalAmount'] + $cost;
                    $userRecord['point'] = $userRecord['point'] + 1;
                    $users->put($key, $userRecord);
                }

                $pointsBar->advance();
            }
            $pointsBar->finish();

            $users = $users->where('point', '>', 0);
            $usersCount = $users->count();

            $bar = $this->output->createProgressBar($usersCount);
            foreach ($users as $userPoint) {
                $userId = $userPoint['user_id'];
                $points = $userPoint['point'];
                $totalAmount = $userPoint['totalAmount'];

                if ($points == 0) {
                    continue;
                }

                try {
                    $userBon = Userbon::create([
                        'bon_id' => $bon->id,
                        'user_id' => $userId,
                        'totalNumber' => $points,
                        'userbonstatus_id' => config('constants.USERBON_STATUS_ACTIVE'),
                    ]);
                } catch (QueryException $e) {
                    $failedCounter++;
                    Log::channel('giveLotteryPointsErrors')->error("Error on inserting points for user $userId");
                    $bar->advance();
                    continue;
                }

                $user = $userBon->user;
                Log::channel('giveLotteryPointsInfo')->info("$points points were given to user $userId with total purchase of ".number_format($totalAmount).' Tomans  with mobile '.$user->mobile);
                $successCounter++;

                if ($points > $maxPoint) {
                    $maxPoint = $points;
                    $maxPointUserId = $userId;
                }
                $bar->advance();
                continue;


            }
            $bar->finish();

            return true;
        }
    }

    public function giveRahBaladPoints()
    {
        {
            $bon = Bon::where('name', $this->bonName)->first();
            if (!isset($bon)) {
                $this->error('Bon not found');
                return null;
            }

            $successCounter = 0;
            $failedCounter = 0;
            $maxPoint = 0;
            $maxPointUserId = '';
            $users = collect();

            $products = [];
            $orderproducts = Orderproduct::query()
                ->where('orderproducttype_id', '<>', config('constants.ORDER_PRODUCT_GIFT'))
                ->whereHas('order', function ($q) {
                    $q->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                        ->whereIn('paymentstatus_id', [
                            config('constants.PAYMENT_STATUS_PAID'),
                            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED')
                        ])
                        ->where('completed_at', '>=', self::SINCE_DATE)
                        ->where('completed_at', '<=', self::TILL_DATE)
                        ->whereDoesntHave('user', function ($q) {
                            $q->whereHas('roles', function ($q2) {
                                $q2->whereIn('name', [config('constants.ROLE_EMPLOYEE'), 'walletGiver', 'admin']);
                            });
                        });
                })->whereIn('product_id', $products)
                ->get();

            $orderproductsCount = $orderproducts->count();
            if (!$this->confirm("$orderproductsCount orderproducts found. Do you wish to continue?", true)) {
                return null;
            }

            $pointsBar = $this->output->createProgressBar($orderproductsCount);
            foreach ($orderproducts as $orderproduct) {
                $cost = $orderproduct->price['final'];
                $user = $orderproduct->order->user;
                if (!isset($user)) {
                    Log::channel('giveLotteryPointsWarnings')->warning('Orderproduct '.$orderproduct->id.' has no owner user!');
                    continue;
                }

                $userRecords = $users->where('user_id', $user->id);
                if ($userRecords->isNotEmpty()) {
                    $pointsBar->advance();
                    continue;
                }

                $users->push([
                    'user_id' => $user->id,
                    'totalAmount' => $cost,
                    'point' => 1,
                ]);

                $pointsBar->advance();
                continue;
            }

            $pointsBar->finish();

            $users = $users->where('point', '>', 0);

            $extraPointsBar = $this->output->createProgressBar($users->count());
            foreach ($users as $key => $userPoint) {
                $user = User::find($userPoint['user_id']);
                if (!isset($user)) {
                    Log::channel('giveLotteryPointsWarnings')->warning('User '.$userPoint['user_id'].' not found for giving extra points');
                    continue;
                }

                $countPurchasedRaheAbrisham = $user->countPurchasedProducts(Product::ALL_SINGLE_ABRISHAM_EKHTESASI_PRODUCTS,
                    self::SINCE_DATE, self::TILL_DATE);
                $userPoint['point'] = $userPoint['point'] + $countPurchasedRaheAbrisham;
                $users->put($key, $userPoint);
            }
            $extraPointsBar->finish();

            $usersCount = $users->count();

            $bar = $this->output->createProgressBar($usersCount);
            foreach ($users as $userPoint) {
                $userId = $userPoint['user_id'];
                $points = $userPoint['point'];
                $totalAmount = $userPoint['totalAmount'];

                if ($points == 0) {
                    continue;
                }

                try {
                    $userBon = Userbon::create([
                        'bon_id' => $bon->id,
                        'user_id' => $userId,
                        'totalNumber' => $points,
                        'userbonstatus_id' => config('constants.USERBON_STATUS_ACTIVE'),
                    ]);
                } catch (QueryException $e) {
                    $failedCounter++;
                    Log::channel('giveLotteryPointsErrors')->error("Error on inserting points for user $userId");
                    $bar->advance();
                    continue;
                }

                $user = $userBon->user;
                Log::channel('giveLotteryPointsInfo')->info("$points points were given to user $userId with total purchase of ".number_format($totalAmount).' Tomans  with mobile '.$user->mobile);
                $successCounter++;

                if ($points > $maxPoint) {
                    $maxPoint = $points;
                    $maxPointUserId = $userId;
                }
                $bar->advance();
                continue;


            }
            $bar->finish();
            return null;
        }
    }
}
