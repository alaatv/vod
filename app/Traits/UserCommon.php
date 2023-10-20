<?php namespace App\Traits;

use App\Classes\Uploader\Uploader;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Order;
use App\Models\Shahr;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\UserRepo;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


trait UserCommon
{
    use CharacterCommon;

    /**
     * Exchange user lottery points
     *
     * @param $user
     * @param $points
     *
     * @return array
     */
    public function exchangeLottery($user, $points): array
    {
        /**   giving coupon */ /**do {
     * $couponCode = str_random(5);
     * }while(Coupon::where("code" , $couponCode)->get()->isNotEmpty());
     *
     * $insertCouponRequest = new \App\Http\Requests\InsertCouponRequest() ;
     * $insertCouponRequest->offsetSet("enable" , 1);
     * $insertCouponRequest->offsetSet("name" , "قرعه کشی همایش ویژه دی برای ".$user->getFullName());
     * $insertCouponRequest->offsetSet("code" , $couponCode);
     * $insertCouponRequest->offsetSet("discount" , config("constants.HAMAYESH_LOTTERY_EXCHANGE_AMOUNT"));
     * $insertCouponRequest->offsetSet("usageNumber" , 0);
     * $insertCouponRequest->offsetSet("usageLimit" , 1);
     * $insertCouponRequest->offsetSet("limitStatus" , 1);
     * $insertCouponRequest->offsetSet("coupontype_id" , 2);
     * $couponProducts = Product::whereNotIn("id" , [167,168,169,174,175,170,171,172,173,179,180,176,177,178])->get()->pluck('id')->toArray();
     * $insertCouponRequest->offsetSet("products" , $couponProducts);
     * $insertCouponRequest->offsetSet("validSince" , "2017-12-14T00:00:00");
     * $insertCouponRequest->offsetSet("validUntil" , "2017-12-19T24:00:00");
     * $result =  $couponController->store($insertCouponRequest)->status() == Response::HTTP_OK
     * if($result)
     * {
     * $attachCouponRequest = new \App\Http\Requests\SubmitCouponRequest() ;
     * $attachCouponRequest->offsetSet("coupon" , $couponCode);
     * $orderController = new \App\Http\Controllers\OrderController();
     * $orderController->submitCoupon($attachCouponRequest) ;
     * session()->forget('couponMessageError');
     * session()->forget('couponMessageSuccess');
     * $prizeName = "کد تخفیف ".$couponCode." با ".config("constants.HAMAYESH_LOTTERY_EXCHANGE_AMOUNT")."% تخفیف( تا تاریخ 1396/09/28 اعتبار دارد )" ;
     * }
     */

        /**   giving credit */
        $unitAmount = config('constants.HAMAYESH_LOTTERY_EXCHANGE_AMOUNT');
        $amount = $unitAmount * $points;
        $depositResult = $user->deposit($amount, config('constants.WALLET_TYPE_GIFT'));
        $done = $depositResult['result'];
        $responseText = $depositResult['responseText'];
        $objectId = $depositResult['wallet'];
        $prizeName = 'مبلغ '.number_format($amount).' تومان اعتبار هدیه';

        return [
            $done,
            $responseText,
            $prizeName,
            $objectId,
        ];
    }

    /**
     * Validates national code
     *
     * @param      $value
     *
     * @param  bool  $canByPass
     *
     * @return bool
     */
    public function validateNationalCode($value, $canByPass = true): bool
    {
        $value = (string) $value;
        // pass allowed National Codes >backdoor<
        $allowedNationalCodes = ['0000000000', '9999999999'];

        if (in_array($value, $allowedNationalCodes) && $canByPass) {
            return true;
        }

        // reject none 10 digits or same repeated digits
        if (!preg_match('/^\d{10}$/', $value) or count(array_count_values(str_split($value))) == 1) {
            return false;
        }

        // parity check algorithm
        $parity = (int) $value[9];
        $sum = array_sum(array_map(function ($x) use ($value) {
                return ((int) $value[$x]) * (10 - $x);
            }, range(0, 8))) % 11;

        return ($sum < 2 && $parity == $sum) || ($sum >= 2 && $parity + $sum == 11);
    }

    /**
     * @param $orders
     *
     * @return Transaction|Builder
     */
    public function getInstalments($orders): Builder
    {
        return Transaction::whereIn('order_id', $orders->pluck('id'))
            ->whereDoesntHave('parents')
            ->where('transactionstatus_id',
                config('constants.TRANSACTION_STATUS_UNPAID'));
    }

    public function usersImport($row)
    {
        // Requirements
        $userInsertionIsFailed = false;
        $userHasBeenUpdated = false;
        $userInsertionReason = [];

        $user = User::where('mobile', Arr::get($row, 'mobile'))
            ->where('nationalCode', Arr::get($row, 'nationalCode'))
            ->first();

        // 1. Prepare user params
        $userParams = $this->UserImportPreparationParams($row, $user);

        // 2. Validation user params
        [$userInsertionIsFailed, $userInsertionReason] = $this->userImportValidation($row, $userInsertionIsFailed,
            $userInsertionReason);

        // 3. User insertion operation
        if (!$userInsertionIsFailed || !empty($user)) {
            [
                $user, $userInsertionIsFailed, $userInsertionReason, $userHasBeenUpdated
            ] = $this->userInsertionOperation($user, $userParams, $userInsertionIsFailed, $userInsertionReason,
                $userHasBeenUpdated);
        }

        return [!empty($user) ? $user : null, $userInsertionIsFailed, $userInsertionReason, $userHasBeenUpdated];
    }

    public function UserImportPreparationParams($row, $user)
    {
        /**
         * @var string $firstName
         * @var string $lastName
         * @var string $gender
         * @var string $ostan
         * @var string $shahr
         * @var string $school
         * @var string $major
         * @var string $grade
         * @var string $mobile
         * @var string $nationalCode
         */
        foreach ($row as $key => $value) {
            $$key = $value;
        }

        $userParams = [
            'firstName' => !empty($firstName) ? $firstName : null,
            'lastName' => !empty($lastName) ? $lastName : null,
            'school' => !empty($school) ? $school : null,
            'gender_id' => !empty($gender) && $gender === 'مرد' ? 1 : (!empty($gender) && $gender === 'زن' ? 2 : null),
            'grade_id' => !empty($grade) && ($gradeQuery = Grade::where('displayName',
                $grade))->exists() ? $gradeQuery->first()->id : null,
            'shahr_id' => !empty($shahr) && ($shahrQuery = Shahr::where('name',
                $shahr))->exists() ? $shahrQuery->first()->id : null,
            'major_id' => !empty($major) && ($majorQuery = Major::where('name',
                $major))->exists() ? $majorQuery->first()->id : null,
        ];

        if (isset($user)) {

            return $userParams;
        }
        $userParams = array_merge($userParams, [
            'mobile' => $mobile,
            'nationalCode' => $nationalCode,
            'password' => bcrypt($nationalCode),
            'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
            'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
        ]);


        return $userParams;
    }

    public function userImportValidation($row, $userInsertionIsFailed, $userInsertionReason)
    {

        $userRules = $this->getInsertUserValidationRules($row->toArray());
        $rules = [
            'mobile' => $userRules['mobile'],
            'nationalCode' => $userRules['nationalCode'],
        ];

        $validator = Validator::make($row->toArray(), $rules);
        if ($validator->fails()) {
            $userInsertionIsFailed = true;
            $userInsertionReason[] = $validator->errors()->first();
        }

        return [$userInsertionIsFailed, $userInsertionReason];
    }

    /**
     * Returns validation rules for inserting a user
     *
     * @param  array  $data
     *
     * @return array
     */
    public function getInsertUserValidationRules(array $data): array
    {
        $rules = [
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'mobile' => [
                'required',
                'digits:11',
                'phone:AUTO,IR',
                Rule::unique('users')
                    ->where(static function ($query) use ($data) {
                        $query->where('nationalCode', Arr::get($data, 'nationalCode'))
                            ->where('deleted_at', null);
                    }),
            ],
            'password' => 'required|min:6',
            'nationalCode' => [
                'required',
                'digits:10',
                'validate:nationalCode',
                Rule::unique('users')
                    ->where(static function ($query) use ($data) {
                        $query->where('mobile', Arr::get($data, 'mobile'))
                            ->where('deleted_at', null);
                    }),
            ],
            'userstatus_id' => 'required|exists:userstatuses,id',
            'photo' => 'image|mimes:jpeg,jpg,png|max:512',
            'postalCode' => 'sometimes|nullable|numeric',
            'major_id' => 'sometimes|nullable|exists:majors,id',
            'gender_id' => 'sometimes|nullable|exists:genders,id',
            'email' => 'sometimes|nullable|email',
        ];

        return $rules;
    }

    public function userInsertionOperation(
        $user,
        $userParams,
        $userInsertionIsFailed,
        $userInsertionReason,
        $userHasBeenUpdated
    ) {
        // Note: We can't use the exception Handler class instead of the following try try catch.
        //  This is because the exception Handler class is currently only used to return one response. And also
        //  we don't want that to happen this and we want the code to continue on its way.
        try {
            if (!empty($user)) {
                $user = UserRepo::update($user, $userParams);

                $userInsertionIsFailed = true;
                $userHasBeenUpdated = true;
            }

            if (empty($user)) {
                $user = UserRepo::create($userParams);

                $userInsertionIsFailed = false;
                $userInsertionReason[] = '';
            }
        } catch (Exception $exception) {
            $userInsertionIsFailed = true;
            $userInsertionReason[] = 'عملیات ثبت با خطا مواجه شد';
        }

        return [$user, $userInsertionIsFailed, $userInsertionReason, $userHasBeenUpdated];
    }

    public function usersOrderImportMapItems($row)
    {
        $indexKeys = [
            'firstName',            // 0
            'lastName',             // 1
            'gender',               // 2
            'ostan',                // 3
            'shahr',                // 4
            'school',               // 5
            'major',                // 6
            'grade',                // 7
            'mobile',               // 8
            'nationalCode',         // 9
//          'insertionStatus',      // 10
//          'insertionFailReason',  // 11
        ];

        foreach ($indexKeys as $index => $key) {
            $row[$key] = trim(Arr::get($row, $index));
            unset($row[$index]);
        }

        $mobile = trim(Arr::get($row, 'mobile'));
        $mobile = $this->convertToEnglish($mobile);
        $row['mobile'] = '0'.baseTelNo($mobile);

        $nationalCode = trim(Arr::get($row, 'nationalCode'));
        $nationalCode = $this->convertToEnglish($nationalCode);
        $row['nationalCode'] = str_pad($nationalCode, 10, '0', STR_PAD_LEFT);

        return $row;
    }

    public function notExistsProducts(User $user, $productIds)
    {
        $remainProducts = [];
        foreach ($productIds as $productId) {
            if (!$this->checkUserOrderProductExists($user, $productId)) {
                $remainProducts[] = $productId;
            }
        }

        return $remainProducts;
    }

    public function checkUserOrderProductExists(User $user, $productId)
    {
        return Order::where('user_id', $user->id)
            ->where('orderstatus_id', config('constants.ORDER_STATUS_CLOSED')) //2
            ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID')) // 5
            ->whereHas('orderproducts', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->first();
    }

    public function create3AOrderForUser(
        User $user,
        ?int $paymentStatusId = null,
        ?int $discount = 0,
        ?int $orderStatusId = null
    ): ?Order {
        try {
            return OrderRepo::createBasicCompletedOrder($user->id, $paymentStatusId, discount: $discount,
                orderStatusId: $orderStatusId);
        } catch (Exception $e) {
            Log::error('order was not created for user '.$user->id);
            Log::error('file:'.$e->getFile().':'.$e->getLine());
        }

        return null;
    }

    public function add3AProductToUser(Order $order, User $user, $products): ?int
    {
        try {
            $orderCost = 0;
            foreach ($products as $product) {
                $productPrice = $product->price;
                $orderCost += $productPrice['base'];
                OrderproductRepo::createBasicOrderproduct($order->id, $product->id, $productPrice['base'],
                    $productPrice['base']);
            }
            return $orderCost;

        } catch (Exception $e) {
            Log::error('order was not created for user '.$user->id.' of products '.implode(',',
                    $products->pluck('id')->toArray()));
            Log::error('file:'.$e->getFile().':'.$e->getLine());
        }

        return null;
    }

    public function addGiftToUser(Order $order, user $user, $giftProducts): ?int
    {
        try {
            $orderCost = 0;
            foreach ($giftProducts as $giftProduct) {
                $orderCost += $giftProduct->price['base'];
                OrderproductRepo::createGiftOrderproduct($order->id, $giftProduct->id, $giftProduct->price['base']);
            }
            return $orderCost;

        } catch (Exception $e) {
            Log::error('order was not created for user '.$user->id.' of products '.implode(',',
                    $giftProducts->pluck('id')->toArray()));
            Log::error('file:'.$e->getFile().':'.$e->getLine());
        }

        return null;
    }

    public function updateOrderCostAndDiscount(Order $order, int $orderProductsCost, ?int $discount = null)
    {
        $order->update([
            'cost' => 0,
            'costwithoutcoupon' => $orderProductsCost,
            'discount' => $discount ?? 0,
        ]);
    }

    /**
     * @param  User  $user
     * @param        $file
     *
     * @return string|null
     * @throws FileNotFoundException
     */
    protected function storePhotoOfUser(User $user, $file): ?string
    {
        return $user->setPhoto($file, config('disks.PROFILE_IMAGE_MINIO'));
    }

    /**
     * @param  User  $user
     * @param        $file
     *
     * @return string|null
     * @throws FileNotFoundException
     */
    protected function storePhotoOfKartemeli(User $user, $file): ?string
    {
        $previousNationalCardPath = $user->getRawOriginal('kartemeli');
        if ($previousNationalCardPath) {
            Uploader::delete(config('disks.KARTE_MELI_IMAGE_MINIO'), $previousNationalCardPath, false);
        }
        $fileName =
            Uploader::makeFolderName().'/'.Carbon::now()->getTimestamp().makeRandomOnlyAlphabeticalString(4).'.'.$file->getClientOriginalExtension();
        return Uploader::put($file, config('disks.KARTE_MELI_IMAGE_MINIO'), null, $fileName);
    }

    /**
     * @param  array  $newRoleIds
     * @param  User  $staffUser
     * @param  User  $user
     */
    protected function syncRoles(array $newRoleIds, User $user): void
    {
        $oldRoles = $user->roles;
        if (isset($oldRoles)) {
            $user->roles()->detach($oldRoles->pluck('id')->toArray());
        }
        $user->roles()->attach($newRoleIds);
    }

    private function getEncryptedProfileEditUrl(string $encryptedPostfix)
    {
        $parameters = [
            'data' => $encryptedPostfix,
        ];

        return URL::temporarySignedRoute(
            'redirectToEditProfileRoute',
            3600,
            $parameters
        );
    }

    /**
     * Checks whether user has default avatar or not
     *
     * @param  string  $photoPath
     *
     * @return bool
     */
    private function isDefaultProfilePhoto(string $photoPath): bool
    {
        return strcmp($photoPath, config('constants.PROFILE_DEFAULT_IMAGE')) == 0;
    }

    private function getUnderAuthorityRoles(User $user): array
    {
        $underAuthorityRoles = [];
        if ($user->hasRole(config('constants.ROLE_ADMIN'))) {
            $underAuthorityRoles[] = config('constants.ROLE_EMPLOYEE');
            $underAuthorityRoles[] = config('constants.ROLE_ADMIN');
        }

        if ($user->hasRole(config('constants.ROLE_ACCOUNTANT'))) {
            $underAuthorityRoles[] = config('constants.ROLE_EMPLOYEE');
        }

        if ($user->hasRole(config('constants.ROLE_EDITOR_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_CAMERA_EMPLOYEE');
            $underAuthorityRoles[] = config('constants.ROLE_STUDIO_MANAGER');
        }

        if ($user->hasRole(config('constants.ROLE_CONTENT_MANAGER'))) {
            $underAuthorityRoles = array_merge($underAuthorityRoles, [
                config('constants.ROLE_CONTENT_EMPLOYEE'), config('constants.ROLE_CONTENT_MANAGER'),
                config('constants.ROLE_MOTION_MANAGER'), config('constants.ROLE_GRAPHIC_MANAGER'),
                config('constants.ROLE_STUDIO_MANAGER'), config('constants.ROLE_MOTEVASETE_DOVOM_MANAGER'),
                config('constants.ROLE_RAHE_ABRISHAM_MANAGER')
            ]);
        }

        if ($user->hasRole(config('constants.ROLE_PUBLIC_RELATION_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_PUBLIC_RELATION_EMPLOYEE');
            $underAuthorityRoles[] = config('constants.ROLE_PUBLIC_RELATION_MANAGER');
        }

        if ($user->hasRole(config('constants.3A_ROLE_TYPIST_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.3A_ROLE_TYPIST_EMPLOYEE');
            $underAuthorityRoles[] = config('constants.3A_ROLE_TYPIST_MANAGER');
        }

        if ($user->hasRole(config('constants.ALAA_ROLE_TYPIST_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ALAA_ROLE_TYPIST_EMPLOYEE');
            $underAuthorityRoles[] = config('constants.ALAA_ROLE_TYPIST_MANAGER');
        }

        if ($user->hasRole(config('constants.ROLE_TECHNICAL_SUPPORT'))) {
            $underAuthorityRoles[] = config('constants.ROLE_TECHNICAL_SUPPORT');
        }

        if ($user->hasRole(config('constants.ROLE_MOTION_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_MOTION_EMOLOYEE');
        }

        if ($user->hasRole(config('constants.ROLE_GRAPHIC_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_GRAPHIC_EMPLOYEE');
        }

        if ($user->hasRole(config('constants.ROLE_MOTEVASETE_DOVOM_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_EDUCATION');
        }

        if ($user->hasRole(config('constants.ROLE_RAHE_ABRISHAM_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_RAHE_ABRISHAM_ASSISTANT');
        }

        if ($user->hasRole(config('constants.ROLE_3A_HEAD_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_3A_EDUCATIONAL_EMPLOYEE');
        }

        if ($user->hasRole(config('constants.ROLE_PROJECT_CONTROLLER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_PROJECT_CONTROLLER');
        }

        return $underAuthorityRoles;
    }

    private function getAllayManagers(User $user): array
    {
        $underAuthorityRoles = [];
        if ($user->hasRole(config('constants.ROLE_PUBLIC_RELATION_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_SUPPORT_MANAGER');
            $underAuthorityRoles[] = config('constants.ROLE_PUBLIC_RELATION_MANAGER');
        }

        if ($user->hasRole(config('constants.ROLE_SUPPORT_MANAGER'))) {
            $underAuthorityRoles[] = config('constants.ROLE_SUPPORT_MANAGER');
            $underAuthorityRoles[] = config('constants.ROLE_PUBLIC_RELATION_MANAGER');
        }

        return $underAuthorityRoles;
    }

    /**
     * @param  User  $user
     *
     * @return mixed
     */
    private function getUserFullName(?User $user): string
    {
        $userFullName = optional($user)->full_name;
        return (isset($userFullName)) ? $userFullName : 'آلایی';
    }
}
