<?php

namespace App\Jobs;

use App\Models\User;
use App\Repositories\CouponRepo;
use App\Traits\CharacterCommon;
use App\Traits\User\AssetTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class InsertExcel implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use CharacterCommon;
    use AssetTrait;

    public const SMS_PATTERN_CODE = 'xqp8wcgc10';
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
     * InsertExcel constructor.
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
        Log::channel('insertExcel')->info('Request received from user: '.$this->authUser->id);

        foreach ($this->array as $row => $item) {
            $code = $this->generateCoupon($item[5]);

            if (!isset($code)) {
                continue;
            }

            if (!$this->validateItemData($item)) {
                Log::channel('insertExcel')->info('incomplete data for order '.Arr::get($item, 0).' , row: '.$row);
                continue;
            }

            $firstName = Arr::get($item, 1);
            $lastName = Arr::get($item, 2);
            $mobileNumber = Arr::get($item, 3);
            $price = Arr::get($item, 4);
            $discount = Arr::get($item, 5);

            $inputData = [
                'name' => $firstName.' '.$lastName,
                'price' => number_format($price),
                'code' => $code,
                'date' => '20 آبان 99',
                'discount' => $discount,
                'supportLink' => route('web.shop'),
            ];

            dispatch(new SendPatternSMS($mobileNumber, self::SMS_PATTERN_CODE, $inputData));
        }

        return null;
    }

    private function generateCoupon(int $discount): ?string
    {
        do {
            $code = 'ch'.random_int(1000, 9999);
            $foundCoupon = CouponRepo::findCouponByCode($code);
        } while (isset($foundCoupon));


        try {
            $coupon = CouponRepo::createBasicOveralCoupon($code, $discount, 'باشگاه مشتریان', 1,
                'برای مشتریان سایت چی بخونم', Carbon::now(), Carbon::parse('2020-11-11 00:00:00'));
        } catch (QueryException $e) {
            Log::channel('insertExcel')->info('Error on creating coupon');
        }

        if (isset($coupon)) {
            return $code;
        }

        return null;
    }

    private function validateItemData(array $item): bool
    {
        return !is_null(Arr::get($item, 1)) && !is_null(Arr::get($item, 2)) && !is_null(Arr::get($item,
                3)) && !is_null(Arr::get($item, 4));
    }
}
