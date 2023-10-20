<?php

namespace App\Jobs;

use App\Classes\CacheFlush;
use App\Collection\ProductCollection;
use App\Events\SendOrderNotificationsEvent;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\School;
use App\Models\Shahr;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketStatus;
use App\Models\User;
use App\Notifications\Parcham;
use App\Notifications\Parcham100FirstNotif;
use App\Notifications\Parcham100SecondNotif;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\TicketMessageRepo;
use App\Repositories\TicketRepo;
use App\Traits\CharacterCommon;
use App\Traits\User\AssetTrait;
use App\Traits\UserCommon;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class InsertBatchOrders implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use CharacterCommon;
    use AssetTrait;
    use UserCommon;

    public const EXCEL_ROW_NO = 0;
    public const EXCEL_FIRST_NAME_COLUMN_NO = 1;
    public const EXCEL_LAST_NAME_COLUMN_NO = 2;
    public const EXCEL_GENDER_COLUMN_NO = 3;
    public const EXCEL_EDUCATIONAL_BASE_COLUMN_NO = 4;
    public const EXCEL_GRADE_COLUMN_NO = 5;
    public const EXCEL_MAJOR_COLUMN_NO = 6;
    public const EXCEL_MOBILE_COLUMN_NO = 7;
    public const EXCEL_PHONE_COLUMN_NO = 8;
    public const EXCEL_BIRTHDATE_COLUMN_NO = 9;
    public const EXCEL_NATIONAL_CODE_COLUMN_NO = 10;
    public const EXCEL_SCHOOL_NAME_COLUMN_NO = 11;
    public const EXCEL_SCHOOL_TYPE_COLUMN_NO = 12;
    public const EXCEL_SCHOOL_CODE_COLUMN_NO = 13;
    public const EXCEL_SCHOOL_PHONE_COLUMN_NO = 14;
    public const EXCEL_SCHOOL_ADDRESS_COLUMN_NO = 15;
    public const EXCEL_SCHOOL_MANAGER_NAME_COLUMN_NO = 16;
    public const EXCEL_SHAHR_ID_COLUMN_NO = 17;
    public const EXCEL_ADDRESS_COLUMN_NO = 18;
    public const EXCEL_POSTAL_CODE_COLUMN_NO = 19;
    public const EXCEL_REGISTER_DATE_COLUMN_NO = 20;
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
     * @var ProductCollection
     */
    private $products;
    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * InsertKMTUsers constructor.
     *
     * @param  array  $array
     * @param  User  $authUser
     * @param  ProductCollection  $products
     * @param  Coupon  $coupon
     */
    public function __construct(array $array, User $authUser, ProductCollection $products, Coupon $coupon)
    {
        $this->array = $array;
        $this->authUser = $authUser;
        $this->products = $products;
        $this->coupon = $coupon;
        $this->queue = 'default2';
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
        Log::channel('batchOrders')->info('Received request from user '.$authUser->id);

        $couponUsageNumber = $this->coupon->usageNumber;
        foreach ($this->array as $row => $item) {
            Log::channel('batchOrders')->info('Begin processing row:'.Arr::get($item,
                    self::EXCEL_ROW_NO).' , user:'.Arr::get($item, self::EXCEL_MOBILE_COLUMN_NO));

            if (!isset($item[self::EXCEL_MOBILE_COLUMN_NO])) {
                Log::channel('batchOrders')->info('Skipped , no mobile found for row:'.Arr::get($item,
                        self::EXCEL_ROW_NO));
                continue;
            }

            if (!isset($item[self::EXCEL_NATIONAL_CODE_COLUMN_NO])) {
                Log::channel('batchOrders')->info('Skipped , no national code found for user , mobile:'.Arr::get($item,
                        self::EXCEL_MOBILE_COLUMN_NO));
                continue;
            }

            try {
                $shahrId = Arr::get($item, self::EXCEL_SHAHR_ID_COLUMN_NO);
                if (!Shahr::find($shahrId)) {
                    Log::channel('batchOrders')->info('Database error on find Shahr ,mobile: '.Arr::get($item,
                            self::EXCEL_MOBILE_COLUMN_NO));
                    continue;
                }
                $school = $this->createOrFindSchool($item, $shahrId);
                $item['school_id'] = $school->id;
                $user = $this->createOrUpdateUser($item);
            } catch (QueryException $e) {
                Log::channel('batchOrders')->info('Database error on creating user ,mobile: '.Arr::get($item,
                        self::EXCEL_MOBILE_COLUMN_NO));
                Log::channel('batchOrders')->error($e->getMessage());
                Log::channel('batchOrders')->error($e->getLine());
                Log::channel('batchOrders')->error($e->getFile());
                continue;
            }

            $purchasedProducts = $user->products();
            $purchasedProductsArray = $purchasedProducts->pluck('id')->toArray();

            try {

                $productsDiffFromPurchased = array_diff($this->products->pluck('id')->toArray(),
                    $purchasedProductsArray);
                if (count($productsDiffFromPurchased) == 0) {
                    Log::channel('batchOrders')->info('User had all selected products, user: '.$user->id);
                    continue;
                }

                $paymentStatus = config('constants.PAYMENT_STATUS_INDEBTED');
//                if(in_array($this->coupon->id, Coupon::PARCHAM_COUPON_1401))
                if ($this->coupon->id == Coupon::PARCHAM_COUPON_1401_100) {
                    $paymentStatus = config('constants.PAYMENT_STATUS_PAID');
                }

                $order =
                    OrderRepo::createBasicCompletedOrder(userId: $user->id, paymentstatus_id: $paymentStatus,
                        costWithoutCoupon: null, costWithCoupon: 0, couponId: $this->coupon->id,
                        couponDiscount: $this->coupon->discount);

                foreach ($this->products as $product) {
                    if (in_array($product->id, $purchasedProductsArray)) {
                        Log::channel('batchOrders')->info('User didnt get product '.$product->id.' because he had it. mobile :'.Arr::get($item,
                                self::EXCEL_MOBILE_COLUMN_NO).' ,user: '.$user->id);
                        continue;
                    }


                    $done = $this->addProductToUser($order, $product);
                    if (!$done) {
                        Log::channel('batchOrders')->info('Error on giving product '.$product->id.' to user: '.$user->id);
                        continue;
                    }

                    Log::channel('batchOrders')->info($product->name.' has been given to the user :'.$user->id);
                }

                if (in_array($paymentStatus, Order::getDoneOrderPaymentStatus())) {
                    event(new SendOrderNotificationsEvent($order, $order->user, true));
                }

                $order->completed_at = Carbon::now('Asia/Tehran');
                $order->discount = 0;
                $order->refreshCostWithoutReobtain();

                foreach ($order->orderproducts as $orderproduct) {
                    $orderproduct->includedInCoupon = 1;
                    $orderproduct->updateWithoutTimestamp();
                }


                CacheFlush::flushAssetCache($user);

                $ticket = $this->createUserTicket($user);
                $this->createAdminTicket($this->authUser, $user, $ticket->id, $order->id);
            } catch (QueryException $e) {
                Log::channel('batchOrders')->info('Database error on giving products ,mobile: '.Arr::get($item,
                        self::EXCEL_MOBILE_COLUMN_NO));
                Log::channel('batchOrders')->error($e->getMessage());
                Log::channel('batchOrders')->error($e->getLine());
                Log::channel('batchOrders')->error($e->getFile());
                continue;
            }

            $this->parchamCouponNotif($user, $order);

            $couponUsageNumber++;
            Log::channel('batchOrders')->info('User successfully processed : '.optional($user)->id.','.optional($user)->mobile);
        }

        $this->coupon->update(['usageNumber' => $couponUsageNumber]);
        Log::channel('batchOrders')->info('End of processing request from user '.$authUser->id);
        Artisan::call('cache:clear');
        return null;
    }

    private function createOrFindSchool(array $item, int $shahrId): School
    {
        $schoolCode = $this->convertToEnglish(Arr::get($item, self::EXCEL_SCHOOL_CODE_COLUMN_NO));
        $schoolInfo = [
            'schoolType_id' => Arr::get($item, self::EXCEL_SCHOOL_TYPE_COLUMN_NO),
            'code' => $schoolCode,
            'phone' => $this->convertToEnglish(Arr::get($item, self::EXCEL_SCHOOL_PHONE_COLUMN_NO)),
            'shahr_id' => $shahrId,
            'address' => Arr::get($item, self::EXCEL_SCHOOL_ADDRESS_COLUMN_NO),
            'managerName' => Arr::get($item, self::EXCEL_SCHOOL_MANAGER_NAME_COLUMN_NO)
        ];

        $school = School::query()->where('code', $schoolCode)->first();
        if (isset($school)) {
            $school->update($schoolInfo);
            return $school;
        }

        return School::query()->create($schoolInfo);
    }

    private function createOrUpdateUser(array $item): User
    {
        $mobile = $this->convertToEnglish(Arr::get($item, self::EXCEL_MOBILE_COLUMN_NO));
        $nationalCode = $this->convertToEnglish(Arr::get($item, self::EXCEL_NATIONAL_CODE_COLUMN_NO));

        $user = User::query()->where('mobile', $mobile)->where('nationalCode', $nationalCode)->first();

        $firstName = !$this->strIsEmpty(Arr::get($item, self::EXCEL_FIRST_NAME_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_FIRST_NAME_COLUMN_NO) : optional($user)->firstName;
        $lastName = !$this->strIsEmpty(Arr::get($item, self::EXCEL_LAST_NAME_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_LAST_NAME_COLUMN_NO) : optional($user)->lastName;
        $majorId = !is_null(Arr::get($item, self::EXCEL_MAJOR_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_MAJOR_COLUMN_NO) : optional($user)->major_id;
        $genderId = !is_null(Arr::get($item, self::EXCEL_GENDER_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_GENDER_COLUMN_NO) : optional($user)->gender_id;
        $gradeId = !is_null(Arr::get($item, self::EXCEL_GRADE_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_GRADE_COLUMN_NO) : optional($user)->grade_id;
        $educationalBaseId = !is_null(Arr::get($item, self::EXCEL_EDUCATIONAL_BASE_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_EDUCATIONAL_BASE_COLUMN_NO) : optional($user)->educationalBase_id;
        $shahrId = !$this->strIsEmpty(Arr::get($item, self::EXCEL_SHAHR_ID_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_SHAHR_ID_COLUMN_NO) : optional($user)->shahr_id;
        $address = !$this->strIsEmpty(Arr::get($item, self::EXCEL_ADDRESS_COLUMN_NO)) ? Arr::get($item,
            self::EXCEL_ADDRESS_COLUMN_NO) : optional($user)->address;
        $postalCode = $this->convertToEnglish(Arr::get($item, self::EXCEL_POSTAL_CODE_COLUMN_NO));
        $postalCode = !$this->strIsEmpty($postalCode) ? $postalCode : optional($user)->postalCode;
        $birthDate = $this->convertToEnglish(Arr::get($item, self::EXCEL_BIRTHDATE_COLUMN_NO));
        $birthDate = !$this->strIsEmpty($birthDate) ? $birthDate : optional($user)->birthdate;
        $phone = $this->convertToEnglish(Arr::get($item, self::EXCEL_PHONE_COLUMN_NO));
        $phone = !$this->strIsEmpty($phone) ? $phone : optional($user)->phone;

        if (isset($user)) {
            $user->update([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'major_id' => $majorId,
                'gender_id' => $genderId,
                'grade_id' => $gradeId,
                'educationalBase_id' => $educationalBaseId,
                'shahr_id' => $shahrId,
                'address' => $address,
                'postalCode' => $postalCode,
                'mobile_verified_at' => Date::now(),
                'phone' => $phone,
                'birthdate' => $birthDate,
                'school_id' => Arr::get($item, 'school_id'),
            ]);
            Log::channel('batchOrders')->info('User had been registered. mobile :'.Arr::get($item,
                    self::EXCEL_MOBILE_COLUMN_NO).' ,user: '.$user->id);

            return $user;
        }

        $user = User::create([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'mobile' => $mobile,
            'phone' => $phone,
            'nationalCode' => $nationalCode,
            'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
            'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
            'password' => bcrypt($nationalCode),
            'major_id' => $majorId,
            'gender_id' => $genderId,
            'grade_id' => $gradeId,
            'educationalBase_id' => $educationalBaseId,
            'shahr_id' => $shahrId,
            'address' => $address,
            'postalCode' => $postalCode,
            'mobile_verified_at' => Date::now(),
            'birthdate' => $birthDate,
            'school_id' => Arr::get($item, 'school_id'),
        ]);

        return $user;
    }

    private function addProductToUser(Order $order, Product $product): bool
    {
        try {
            $priceInfo = $product->price;
            OrderproductRepo::createBasicOrderproduct($order->id, $product->id, $priceInfo['base'], $priceInfo['base']);
            $done = true;

        } catch (Exception $e) {
            $order->delete();
            Log::channel('batchOrders')->error('file:'.$e->getFile().':'.$e->getLine());
            $done = false;
        }

        return $done;
    }

    private function createUserTicket(User $user): Ticket
    {
        $title = 'درخواست طرح پرچم';
        $statusId = TicketStatus::STATUS_ANSWERED;
        $departmentId = TicketDepartment::PARCHAM_DEPARTMENT;
        $ticket = TicketRepo::new($user->id, $title, $statusId, $departmentId, null, null, null, null, null, null);
        TicketMessageRepo::new($ticket->id, $user->id, 'درخواست قرار گرفتن در طرح پرچم را دارم', null);
        return $ticket;
    }

    private function createAdminTicket(User $authUser, User $user, int $ticketId, int $orderId)
    {

        $message = $this->parchamCouponMessage($user, $orderId);


        TicketMessageRepo::new($ticketId, $authUser->id, $message, null);
    }

    /**
     * @param  User  $user
     * @param  int  $orderId
     * @param  int  $ticketId
     * @return string
     */
    private function parchamCouponMessage(User $user, int $orderId): string
    {
        /**
         * @var string $userFullName
         * @var string $paymentLink
         * @var Ticket $ticket
         */
        $userFullName = $this->getUserFullName($user);
        $paymentLink = route('redirectToBank',
            ['paymentMethod' => 'mellat', 'device' => 'web', 'order_id' => $orderId]);

        $message = '';
        switch ($this->coupon->id) {
            case Coupon::PARCHAM_COUPON_1401_90:
            case Coupon::PARCHAM_COUPON_1401_50:
                $message = $userFullName.' عزیز ، به جمع راه ابریشمی های آلاء خوش آمدید.'.'<br>'.
                    'درخواست شما برای استفاده از طرح پرچم تایید شد.'.'<br>'.
                    'لطفا جهت پرداخت '.(100 - $this->coupon->discount).'% باقی مانده، به لینک زیر مراجعه کنید.'.'<br>'.$paymentLink;
                break;
            case Coupon::PARCHAM_COUPON_1401_100:
                $productLink = route('web.user.asset');
                $plan = 'پرچم';

                $message = $userFullName.' عزیز ، به جمع راه ابریشمی های آلاء خوش آمدید. لینک دسترسی به دوره: '.$productLink.'<br>'.
                    'خانواده پنج میلیونی آلاء تو هر شرایطی کنار هم هستند و با هم جشن می‌گیرند. آلاء ازت انتظار داره در طرح '.$plan.' وقتی در راه ابریشم به مقصد رسیدی، دلیلی برای جشن گرفتن کل این خانواده باشی انشاءالله 💛'.'<br>';
                break;
            default:
                break;
        }

        return $message;
    }

    /**
     * @param  User  $user
     * @param  Order  $order
     */
    private function parchamCouponNotif(User $user, Order $order)
    {
        Log::channel('batchOrders')->info('Determining suitable notification');
        switch ($this->coupon->id) {
            case Coupon::PARCHAM_COUPON_1401_90:
            case Coupon::PARCHAM_COUPON_1401_50:
                $user->notify(new Parcham($order->id, 100 - $this->coupon->discount));
                Log::channel('batchOrders')->info('Parcham notification was sent');
                break;
            case Coupon::PARCHAM_COUPON_1401_100:
                $user->notify(new Parcham100FirstNotif());
                $user->notify(new Parcham100SecondNotif());
                break;
            default:
                break;
        }
    }
}
