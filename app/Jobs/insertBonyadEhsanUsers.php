<?php

namespace App\Jobs;

use App\Classes\CacheFlush;
use App\Events\SendOrderNotificationsEvent;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\UserRegisterd;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Traits\CharacterCommon;
use App\Traits\User\AssetTrait;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class insertBonyadEhsanUsers implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use CharacterCommon;
    use AssetTrait;

    /**
     *
     * @var array
     */
    private $array;

    /**
     *
     * @var User
     */
    private $authUser;

    /**
     * insertBonyadEhsanUsers constructor.
     *
     * @param  array  $array
     * @param  User  $authUser
     */
    public function __construct(array $array, User $authUser)
    {
        $this->array = $array;
        $this->authUser = $authUser;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $authUser = $this->authUser;

        Log::channel('BonyadEhsanLogs')->info('Received request from user '.$authUser->id);

        $array = $this->array;


        foreach ($array as $row => $item) {
            /**
             * 0 => row number
             * 1 => firstName
             * 2 => lastName
             * 3 => gender
             * 4 => major
             * 5 => mobile
             * 6 => nationalCode
             * 7 => province
             * 8 => city
             */
            $mobile = Arr::get($item, 5);
            $nationalCode = Arr::get($item, 6);

            if (!isset($mobile)) {
                Log::channel('BonyadEhsanLogs')->info('Skipped , no mobile found for row:'.Arr::get($item, 0));
                continue;
            }

            if (!isset($nationalCode)) {
                Log::channel('BonyadEhsanLogs')->info('Skipped , no national code found for user , mobile:'.$mobile);
                continue;
            }

            $byPass = false;
            switch (Arr::get($item, 4)) {
                case 1:
                    $products = Product::query()->whereIn('id',
                        [Product::RAHE_ABRISHAM99_PACK_RIYAZI, Product::RAHE_ABRISHAM1401_PACK_OMOOMI])->get();
                    break;
                case 2:
                    $products = Product::query()->whereIn('id',
                        [Product::RAHE_ABRISHAM99_PACK_TAJROBI, Product::RAHE_ABRISHAM1401_PACK_OMOOMI])->get();
                    break;
                case 3 :
                    $products = Product::query()->where('id', Product::RAHE_ABRISHAM1401_PACK_OMOOMI)->get();
                    break;
                default:
                    $byPass = true;
            }

            if ($byPass) {
                Log::channel('BonyadEhsanLogs')->info('Major not found, mobile: '.$mobile);
                continue;
            }

            $province = Arr::get($item, 7);
            $city = Arr::get($item, 8);

            try {
                $user = User::where('mobile', $mobile)->where('nationalCode', $nationalCode)->first();
                if (isset($user)) {
                    $updateResult = $user->update([
                        'firstName' => Arr::get($item, 1),
                        'lastName' => Arr::get($item, 2),
                        'major_id' => Arr::get($item, 4),
                        'gender_id' => Arr::get($item, 3),
                        'grade_id' => 8,
//                        'province'              => (!$this->strIsEmpty($province))?$province:null,
//                        'city'                  => (!$this->strIsEmpty($city))?$city:null,
                        'mobile_verified_at' => Date::now(),
                    ]);
                    Log::channel('BonyadEhsanLogs')->info('User had been registered. mobile :'.$mobile.' ,user: '.$user->id);
                } else {
                    $user = User::create([
                        'firstName' => Arr::get($item, 1),
                        'lastName' => Arr::get($item, 2),
                        'mobile' => $mobile,
                        'nationalCode' => $nationalCode,
                        'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                        'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                        'password' => bcrypt(Arr::get($item, 6)),
                        'major_id' => Arr::get($item, 4),
                        'gender_id' => Arr::get($item, 3),
                        'grade_id' => 8,
//                        'province'              => (!$this->strIsEmpty($province))?$province:null,
//                        'city'                  => (!$this->strIsEmpty($city))?$city:null,
                        'mobile_verified_at' => Date::now(),
                    ]);
                    $user->notify(new UserRegisterd());
                }

            } catch (QueryException $e) {
                Log::channel('BonyadEhsanLogs')->info('Database error on creating user ,mobile: '.$mobile);
                continue;
            }

            $order = $this->addProductsToUser($user, $products);
            if (is_null($order)) {
                Log::channel('BonyadEhsanLogs')->info('error on creating order for user: '.$user->id);
                continue;
            }


            CacheFlush::flushAssetCache($user);

            event(new SendOrderNotificationsEvent($order, $user));
            // TODO notify user
        }

        Log::channel('BonyadEhsanLogs')->info('End of processing request from user '.$authUser->id);
        return null;
    }

    private function addProductsToUser(User $user, Collection $products): ?Order
    {
        try {
            $order = OrderRepo::createBasicCompletedOrder($user->id,
                config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'), null, null, null, 0);

            $orderPrice = 0;
            foreach ($products as $product) {
                $price = $product->price;
                $orderPrice += $price;
                OrderproductRepo::createBasicOrderproduct($order->id, $product->id, $price['base'], $price['base'], 1);
            }

            $order->update([
                'cost' => $orderPrice,
                'costwithoutcoupon' => 0,
                'coupon_id' => Coupon::BONYAD_EHSAN_COUPON,
                'couponDiscount' => 100,
            ]);

            return $order;

        } catch (Exception $e) {
            $order->delete();
            Log::channel('BonyadEhsanLogs')->error('file:'.$e->getFile().':'.$e->getLine());
            return null;
        }
    }
}
