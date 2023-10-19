<?php

namespace App\Http\Controllers\Api;

use App\Classes\CacheFlush;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiveSMSRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReferralCode;
use App\Models\SMS;
use App\Models\SmsBlackList;
use App\Models\SmsProvider;
use App\Models\User;
use App\Notifications\ArashStudyPlanGuide;
use App\Notifications\GeneralNotice;
use App\Notifications\HasntBoughtProduct;
use App\Notifications\MobileVerified;
use App\Notifications\ProductAddedToUser;
use App\Notifications\ReferralCodeDescription;
use App\Notifications\ReferralCodeRequestAccepted;
use App\Notifications\ReferralCodeRequestRejected;
use App\Notifications\ReferralCount;
use App\Notifications\ReferralIncome;
use App\Notifications\SMSRequstPendingToReview;
use App\Notifications\SuggestLogin;
use App\Notifications\SuggestRegister;
use App\Notifications\UserDoesntHaveVerifiedAccount;
use App\Repositories\OrderproductRepo;
use App\Repositories\SmsBlackListRepository;
use App\Services\WalletService;
use App\Traits\Helper;
use App\Traits\UserCommon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ReceiveSMSController extends Controller
{
    use UserCommon;
    use Helper;

    /**
     * ReceiveSMSController constructor.
     */
    public function __construct()
    {
        $this->middleware('convert:from|to|msg');
    }

    /**
     * Handle the incoming request.
     *
     * @param  ReceiveSMSRequest  $request
     *
     * @return JsonResponse
     */
    public function __invoke(ReceiveSMSRequest $request, WalletService $walletService)
    {
        $from = $request->get('from');
        $from = baseTelNo($from);
        $from = '0'.$from;
        $to = $request->get('to');
        $msg = $request->get('msg');

        Log::channel('receiveSMS')->info('Message received: '.$from.' , '.$msg);

        $msg = $this->purifyMessage($msg);

        [$msgAnalytics, $msgObj] = $this->analyseMessage($msg);

        if ($msgAnalytics == 'disableSMS') {
            SmsBlackListRepository::create(['mobile' => $from]);
            return response()->json(['message' => 'OK']);
        }

        $users = User::where('mobile', $from)->get();
        $usersCount = $users->count();
        if ($users->isEmpty()) {
            Notification::route('mobile', $from)->notify(new SuggestRegister());
            $this->saveSms($msg, $from, $to);

            Log::channel('receiveSMS')->info('User not found');
            return myAbort(Response::HTTP_BAD_REQUEST, 'User not found');
        }

        [$msgAnalytics, $msgObj] = $this->analyseMessage($msg);

        if ($msgAnalytics === 'referral_description') {
            $verifiedUsers = $users->whereNotNull('mobile_verified_at');
            if ($verifiedUsers->isEmpty()) {
                $this->saveSms($msg, $from, $to);
                Log::channel('receiveSMS')->info('User doesnt verified mobile');
                return myAbort(Response::HTTP_BAD_REQUEST, 'User doesnt verified mobile');
            }
            $user = $verifiedUsers->first();
            if ($user->referralRequests->isEmpty()) {
                return response()->json(['message' => 'OK']);
            }

            $user->notify(new ReferralCodeDescription());
            $this->saveSms($msg, $from, $to, $user->id);
            return response()->json(['message' => 'OK']);
        }

        if ($msgAnalytics === 'referral_request') {
            $verifiedUser = User::where('mobile', $from)->whereNotNull('mobile_verified_at')->with([
                'referralRequests.referralCodes' => function ($query) {
                    $query->where('enable', 1)->where('isAssigned', 0);
                }
            ])->whereHas('referralRequests', function ($query) {
                $query->whereHas('referralCodes', function ($query) {
                    $query->where('enable', 1)->where('isAssigned', 0);
                });
            })->first();
            if (!isset($verifiedUser)) {
                $verifiedUsers = $users->whereNotNull('mobile_verified_at');
                if ($verifiedUsers->isEmpty()) {
                    $this->saveSms($msg, $from, $to);
                    Log::channel('receiveSMS')->info('User doesnt verified mobile');
                    return myAbort(Response::HTTP_BAD_REQUEST, 'User doesnt verified mobile');
                }
                $user = $verifiedUsers->first();
                $user->notify(new ReferralCodeRequestRejected());
                $this->saveSms($msg, $from, $to, $user->id);
                return response()->json(['message' => 'OK']);
            }
            $referralCode = [];
            foreach ($verifiedUser->referralRequests as $referralRequest) {
                $referralCode[] = $referralRequest->referralCodes->first();
            }
            /** @var ReferralCode $referralCode */
            $referralCode = Arr::get($referralCode, 0);
            if (!isset($referralCode)) {
                return response()->json(['message' => 'OK']);
            }
            $referralCode->update([
                'isAssigned' => 1,
                'assignor_id' => $verifiedUser->id,
                'assignor_device_id' => config('constants.DEVICE_TYPE_SMS'),
            ]);
            $verifiedUser->notify(new ReferralCodeRequestAccepted("https://alaatv.com/referralCode/{$referralCode->id}/photo",
                $referralCode->code));
            $this->saveSms($msg, $from, $to, $verifiedUser->id);
            return response()->json(['message' => 'OK']);
        }

        if ($msgAnalytics === 'referral_income') {
            $verifiedUsers = $users->whereNotNull('mobile_verified_at');
            if ($verifiedUsers->isEmpty()) {
                $this->saveSms($msg, $from, $to);
                Log::channel('receiveSMS')->info('User doesnt verified mobile');
                return myAbort(Response::HTTP_BAD_REQUEST, 'User doesnt verified mobile');
            }
            $user = $verifiedUsers->first();
            if ($user->referralRequests->isEmpty()) {
                return response()->json(['message' => 'OK']);
            }
            $wallets = $walletService->getWalletsByUserId($user->id);
            $userMainWallet = array_filter_collapse($wallets['data'], function ($wallet) {
                return $wallet['withdrawable'] == true;
            });
            $walletBalance = !empty($userMainWallet) ? $userMainWallet['available-asset'] : 0;
            $totalCommission = $walletService->getUserTotalIncomebyUserId($user->id)['data']['total-income'];
            $incomeBeingSettle =
                $walletService->getUserTotalPendingIncomeByUserId($user->id)['data']['total-pending-withdraw-request'];
            $user->notify(new ReferralIncome($totalCommission, $walletBalance, $incomeBeingSettle));
            $this->saveSms($msg, $from, $to, $user->id);
            return response()->json(['message' => 'OK']);
        }

        if ($msgAnalytics === 'referral_count') {
            $verifiedUsers = $users->whereNotNull('mobile_verified_at');
            if ($verifiedUsers->isEmpty()) {
                $this->saveSms($msg, $from, $to);
                Log::channel('receiveSMS')->info('User doesnt verified mobile');
                return myAbort(Response::HTTP_BAD_REQUEST, 'User doesnt verified mobile');
            }
            $user = $verifiedUsers->first();
            $referralCodes = ReferralCode::whereHas('referralRequest', function ($query) use ($user) {
                $query->where('owner_id', $user->id);
            })->get();
            if ($referralCodes->isEmpty()) {
                return response()->json(['message' => 'OK']);
            }
            $countOfTotalGiftCards = $referralCodes->count();
            $countOfUsedGiftCards = $referralCodes->where('usageNumber', '>', 0)->count();
            $user->notify(new ReferralCount($countOfTotalGiftCards, $countOfUsedGiftCards));
            $this->saveSms($msg, $from, $to, $user->id);
            return response()->json(['message' => 'OK1']);
        }

        if ($msgAnalytics == 'undefined') {
            $verifiedUsers = $users->whereNotNull('mobile_verified_at');
            if ($verifiedUsers->isEmpty()) {
                $this->saveSms($msg, $from, $to);
                Log::channel('receiveSMS')->info('Undefined message');
                return myAbort(Response::HTTP_BAD_REQUEST, 'Unprocessable message');
            }

            $this->saveSms($msg, $from, $to, $verifiedUsers->first()->id);

            Log::channel('receiveSMS')->info('Undefined message');
            return myAbort(Response::HTTP_BAD_REQUEST, 'Unprocessable message');
        }

        if ($msgAnalytics == 'product') {
            $packOrders = Order::query()->whereIn('user_id', $users->pluck('id')->toArray())
                ->whereHas('orderproducts', function ($q) use ($msgObj) {
                    $q->whereIn('product_id',
                        Product::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI_AND_ARASH_1400_PACKS[$msgObj->id]);
                })
                ->where('paymentstatus_id', '<>', config('constants.PAYMENT_STATUS_UNPAID'))
                ->whereIn('orderstatus_id', Order::getDoneOrderStatus())
                ->get();

            $ordersCount = $packOrders->count();

            if ($ordersCount == 0) {
                $user = $users->first();
                $user->notify(new HasntBoughtProduct($this->getRequirement($msgObj->id),
                    $this->getLandingLinks($msgObj->id)));
                $verifiedUsers = $users->whereNotNull('mobile_verified_at');
                if ($verifiedUsers->isEmpty()) {
                    $this->saveSms($msg, $from, $to);
                    Log::channel('receiveSMS')->info('No orders of Riyazi Tajrobi Package found');
                    return myAbort(Response::HTTP_BAD_REQUEST, 'Unprocessable message');
                }

                $this->saveSms($msg, $from, $to, $verifiedUsers->first()->id);

                Log::channel('receiveSMS')->info('No orders of Riyazi Tajrobi Package found');
                return myAbort(Response::HTTP_BAD_REQUEST, 'Invalid request');
            }

            if ($usersCount == 1) {
                $user = $users->first();
                foreach ($packOrders as $packOrder) {
                    $orderproducts = $packOrder->orderproducts;
                    $takAbrishamOrderproducts = $orderproducts->whereIn('product_id',
                        Product::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI_AND_ARASH_1400_SINBLES[$msgObj->id]);
                    if ($takAbrishamOrderproducts->isNotEmpty()) {
                        Log::channel('receiveSMS')->info('Had got product before; only one account . nationalCode: '.$user->nationalCode);
                        continue;
                    }

                    $product = Product::find($msgObj->id);
                    $price = Arr::get($product->price, 'base', 0);
                    $newOrderproduct = orderproductRepo::createHiddenOrderproduct($packOrder->id, $msgObj->id, $price,
                        $price, 0, 0, $orderproducts->first()->includedInInstalments);

                    if (!isset($newOrderproduct)) {
                        Log::channel('receiveSMS')->info('Database error on creating orderproduct , nationalCode: '.$user->nationalCode);
                        $userFullName = $this->getUserFullName($user);
                        $user->notify(new GeneralNotice($userFullName.' عزیز متاسفانه در حال حاضر خطایی در سیستم وجود دارد . لطفا ساعتی دیگر درخواست خود را دوباره ارسال کنید.'));
                        return myAbort(Response::HTTP_BAD_REQUEST, 'Invalid request');
                    }
                }

                Log::channel('receiveSMS')->info('Product given; only one account , nationalCode: '.$user->nationalCode);
                Cache::tags('order_'.$packOrder->id)->flush();
                CacheFlush::flushAssetCache($user);
                $user->notify(new ProductAddedToUser($msgObj->name));

                if (!$user->hasVerifiedMobile()) {
                    $user->markMobileAsVerified();
                }

                $this->saveSms($msg, $from, $to, $user->id);

                return response()->json(['message' => 'OK']);
            }

            $verifiedUsers = $users->whereIn('id',
                $packOrders->pluck('user_id')->toArray())->whereNotNull('mobile_verified_at');
            $verifiedUsersCount = $verifiedUsers->count();
            if ($verifiedUsersCount == 0) {
                $users->first()->notify(new UserDoesntHaveVerifiedAccount($msgObj->name, $msg));
                $this->saveSms($msg, $from, $to);
                Log::channel('receiveSMS')->info('User has no verified accounts');
                return myAbort(Response::HTTP_BAD_REQUEST, 'Invalid request');
            }

            if ($verifiedUsersCount == 1) {
                /** @var User $user */
                $user = $verifiedUsers->first();
                $this->saveSms($msg, $from, $to, $user->id);

                foreach ($packOrders as $packOrder) {
                    if (!isset($packOrder)) {
                        Log::channel('receiveSMS')->info('No orders of Riyazi Tajrobi Package found for verified user');
                        return myAbort(Response::HTTP_BAD_REQUEST, 'Invalid request');
                    }

                    $orderproducts = $packOrder->orderproducts;

                    $takAbrishamOrderproducts = $orderproducts->whereIn('product_id',
                        Product::USER_RECEIVABLE_PRODUCTS_RAHE_ABRISHAM_EKHTESASI_AND_ARASH_1400_SINBLES[$msgObj->id]);
                    if ($takAbrishamOrderproducts->isNotEmpty()) {
                        Log::channel('receiveSMS')->info('Had got product before; multiple account one verified . nationalCode: '.$user->nationalCode);
                        return myAbort(Response::HTTP_BAD_REQUEST, 'Invalid request');
                    }

                    $product = Product::find($msgObj->id);
                    $price = Arr::get($product->price, 'base', 0);

                    $newOrderproduct = orderproductRepo::createHiddenOrderproduct($packOrder->id, $msgObj->id, $price,
                        $price, 0, 0, $orderproducts->first()->includedInInstalments);
                    if (!isset($newOrderproduct)) {
                        $userFullName = $this->getUserFullName($user);
                        $user->notify(new GeneralNotice($userFullName.' عزیز متاسفانه در حال حاضر خطایی در سیستم وجود دارد . لطفا ساعتی دیگر درخواست خود را دوباره ارسال کنید.'));
                        Log::channel('receiveSMS')->info('Database error on creating orderproduct , nationalCode: '.$user->nationalCode);
                        return myAbort(Response::HTTP_BAD_REQUEST, 'Invalid request');
                    }
                }

                $user->notify(new ProductAddedToUser($msgObj->name));
                Cache::tags('order_'.$packOrder->id)->flush();
                CacheFlush::flushAssetCache($user);

                Log::channel('receiveSMS')->info('Product given; multiple account one verified  , nationalCode: '.$user->nationalCode);
                return myAbort(Response::HTTP_BAD_REQUEST, 'Ok');
            }

            $storeSMS = $this->saveSms("{$msg}_pending", $from, $to, $verifiedUsers->first()->id);
            $verifiedUsers->first()->notify(new SMSRequstPendingToReview($storeSMS->id));
            Log::channel('receiveSMS')->info('User has multiple accounts, request stored for later review');
            return response()->json(['message' => 'OK']);
        }

        if ($msgAnalytics == 'nationalCode') {
            $user = $users->where('nationalCode', $msgObj)->first();
            if (!isset($user)) {
                $this->saveSms($msg, $from, $to);
                Notification::route('mobile', $from)->notify(new SuggestLogin());
                Log::channel('receiveSMS')->info('nationalCode not found');
                return myAbort(Response::HTTP_BAD_REQUEST, 'No user found with this nationalCode');
            }

            $this->saveSms($msg, $from, $to, $user->id);

            if (!$user->hasVerifiedMobile()) {
                $user->markMobileAsVerified();
                $user->notify(new MobileVerified());
                Cache::tags('user_'.$user->id)->flush();
                Log::channel('receiveSMS')->info('nationalCode verified');
                return response()->json(['message' => 'OK']);
            }

            Log::channel('receiveSMS')->info('nationalCode had been verified before');
            return response()->json(['message' => 'OK']);
        }

        if ($msgAnalytics != 'arashLinkRequest') {

            Log::channel('receiveSMS')->info('User envolved in none of the conditions');
            return response()->json(['message' => 'OK']);
        }

        $verifiedUsers = $users->whereNotNull('mobile_verified_at');
        if ($verifiedUsers->isNotEmpty()) {
            $user = $verifiedUsers->first();
        }

        if (!isset($user)) {
            $user = $users->first();
        }

        $this->saveSms($msg, $from, $to, $user->id);
        Log::channel('receiveSMS')->info('Request for arash guide');

        $user->notify(new ArashStudyPlanGuide('plink.ir/alaaArashGuide', 'plink.ir/alaaMyProductGuide',
            'plink.ir/alaaBookmarkGuide', route('web.barname', ['studyEventName' => 'a99'])));
        return response()->json(['message' => 'OK']);
    }

    private function purifyMessage(string $msg): string
    {
        $msg = strtolower($msg);
        $msg = strip_tags($msg);
        $msg = strip_punctuations($msg);

        return $msg;
    }

    private function analyseMessage(string $msg)
    {
        if ($this->userWantDisableSms($msg)) {
            return ['disableSMS', true];
        }

        $msgNationalCode = $this->getNationalCodeFromMessage($msg);
        if (isset($msgNationalCode)) {
            return ['nationalCode', $msgNationalCode];
        }

        $chosenProduct = $this->getChosenProduct($msg);
        if (isset($chosenProduct)) {
            return ['product', $chosenProduct];
        }

        $isArashLinkRequest = $this->isArashLinkRequest($msg);
        if ($isArashLinkRequest) {
            return ['arashLinkRequest', $msg];
        }
        $lowerMsg = strtolower($msg);
        return match (true) {
            $lowerMsg === 'g0' => ['referral_description', $msg],
            $lowerMsg === 'g1' => ['referral_request', $msg],
            $lowerMsg === 'g2' => ['referral_income', $msg],
            $lowerMsg === 'g3' => ['referral_count', $msg],
            default => ['undefined', null]
        };
    }

    private function userWantDisableSms(string &$msg): bool
    {
        return Str::contains($msg, SmsBlackList::DISABLE_SMS_WORDS) || Str::is(SmsBlackList::DISABLE_SMS_CHARACTERS,
                $msg);
    }

    private function getNationalCodeFromMessage(string $msg): ?string
    {
        if (!$this->validateNationalCode($msg, true)) {
            return null;
        }

        return $msg;
    }

    /**
     * @param  string  $msg
     * @return Product|null
     */
    private function getChosenProduct(string $msg): ?Product
    {
        switch ($msg) {
            case '1001':
                $productId = Product::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_SABETI;
                break;
            case '1002':
                $productId = Product::RAHE_ABRISHAM1402_RIYAZIAT_TAJROBI_NABAKHTE;
                break;
            case '1003':
                $productId = Product::ARASH_FIZIK_1400_TOLOUYI;
                break;
            case '1004':
                $productId = Product::ARASH_FIZIK_1400;
                break;
            case '1005':
                $productId = Product::RAHE_ABRISHAM99_FIZIK_RIYAZI;
                break;
            case '1006':
                $productId = Product::RAHE_ABRISHAM99_FIZIK_TAJROBI;
                break;
            case '1007':
                $productId = Product::ARASH_FIZIK_1400_TOLOUYI;
                break;
            case '1008':
                $productId = Product::ARASH_FIZIK_1400;
                break;
            case '1009':
                $productId = Product::ARASH_FIZIK_1401_YARI;
                break;
            case '2001':
                $productId = Product::RAHE_ABRISHAM1402_HESABAN_SABETI;
                break;
            case '2002':
                $productId = Product::RAHE_ABRISHAM1402_HESABAN_NABAKHTE;
                break;
            default:
                return null;
        }

        if ($product = Product::find($productId)) {
            return $product;
        }

        return null;
    }

    private function isArashLinkRequest(string $msg)
    {
        return $msg == '202';
    }

    /**
     * @param  string  $message
     * @param  string  $from
     * @param  string  $to
     * @param  int|null  $fromUserId
     *
     * @return false|Builder|Model
     */
    private function saveSms(string $message, string $from, string $to, ?int $fromUserId = null)
    {
        $fromBaseTelNo = baseTelNo($from);
        $toBaseTelNo = baseTelNo($to);
        if (!isset($fromUserId) && ($user = User::where('mobile', 'like', "%{$fromBaseTelNo}%"))->exists()) {
            $fromUserId = $user->first()->id;
        }

        try {
            $sms = SMS::query()->create([
                'from' => $from,
                'from_user_id' => $fromUserId,
                'message' => $message,
                'provider_id' => SmsProvider::query()->where('number', 'like', "%{$toBaseTelNo}%")->first()->id,
                'sent' => 0,
            ]);
        } catch (Exception $e) {
            return false;
        }

        return $sms;
    }

    private function getRequirement(int $productId): ?string
    {
        $map = [
            Product::RAHE_ABRISHAM99_FIZIK_RIYAZI => 'پک تخصصی راه ابریشم',
            Product::RAHE_ABRISHAM99_FIZIK_TAJROBI => 'پک تخصصی راه ابریشم',
            Product::ARASH_FIZIK_1400 => 'پک تخصصی همایش آرش',
            Product::ARASH_FIZIK_1400_TOLOUYI => 'پک تخصصی همایش آرش',
        ];

        return Arr::get($map, $productId);
    }

    private function getLandingLinks(int $productId): ?string
    {
        $map = [
            Product::RAHE_ABRISHAM99_FIZIK_RIYAZI => route('web.landing.25'),
            Product::RAHE_ABRISHAM99_FIZIK_TAJROBI => route('web.landing.25'),
            Product::ARASH_FIZIK_1400 => route('web.landing.15'),
            Product::ARASH_FIZIK_1400_TOLOUYI => route('web.landing.15'),
        ];

        return Arr::get($map, $productId);
    }
}
