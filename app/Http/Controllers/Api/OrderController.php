<?php

namespace App\Http\Controllers\Api;

use App\Classes\CacheFlush;
use App\Classes\CouponSubmitter;
use App\Classes\Pricing\Alaa\AlaaInvoiceGenerator;
use App\Classes\ReferralCodeSubmitter;
use App\Collection\OrderCollections;
use App\Collection\OrderproductCollection;
use App\Events\SendOrderNotificationsEvent;
use App\Events\UserPurchaseCompleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateFreeOrderFor3aRequest;
use App\Http\Requests\DonateRequest;
use App\Http\Requests\order\InsertFreeOrderRequest;
use App\Http\Requests\SubmitCouponRequest;
use App\Http\Requests\SubmitReferralCodeRequest;
use App\Http\Requests\UserSubscriptionRequest;
use App\Http\Resources\CouponInfoWithPrice as CouponInfoResource;
use App\Http\Resources\Invoice as InvoiceResource;
use App\Http\Resources\InvoiceWithOnlyPrice as InvoicePriceResource;
use App\Http\Resources\ReferralCodeInfoWithPrice;
use App\Models\_3aExam;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Product;
use App\Models\ReferralCode;
use App\Models\User;
use App\PaymentModule\GtmEec;
use App\Repositories\CouponRepo;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductRepository;
use App\Services\OrderProductsService;
use App\Traits\CharacterCommon;
use App\Traits\OrderCommon;
use App\Traits\OrderproductTrait;
use App\Traits\User\AssetTrait;
use App\Traits\User\ResponseFormatter;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;

class OrderController extends Controller
{
    use ResponseFormatter;
    use OrderCommon;
    use OrderproductTrait;
    use AssetTrait;
    use CharacterCommon;

    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        $this->middleware(['OverwriteOrderIDAndAddItToRequest', 'openOrder'],
            ['only' => ['submitCoupon', 'submitCouponV2', 'submitReferralCode'],]);
        $this->middleware('OverwriteOrderIDAndAddItToRequest',
            ['only' => ['removeCoupon', 'removeCouponV2', 'removeReferralCode'],]);
        $this->middleware('ApiOrderCheckoutReview', ['only' => 'checkoutReviewV2']);
    }

    /**
     * Showing authentication step in the checkout process
     *
     * @param  Request  $request
     *
     * @return Response
     * @throws Exception
     */
    public function checkoutReview(Request $request)
    {
        $user = $request->user('api');

        $order = $user->getOpenOrderOrCreate();

        $invoiceGenerator = new AlaaInvoiceGenerator();

        $invoiceInfo = $invoiceGenerator->generateOrderInvoice($order);
        unset($invoiceInfo['price']['payableByWallet']);

        return response($invoiceInfo);
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     *
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function checkoutReviewV2(Request $request)
    {
        /** @var User $user */
        $user = $request->user('api');
        if (isset($user)) {
            $order = $user->getOpenOrderOrCreate($request->input('isInInstalment', 0), $request->input('seller', 1));
            $credit = $user->getTotalWalletBalance();
            $orderHasDonate = $order->hasDonate();
            $invoiceInfo = ((new AlaaInvoiceGenerator())->generateOrderInvoice($order));

            $coupon = $order->coupon;
            if (isset($coupon)) {
                $order = $order->restoreCoupon($coupon);
                $void = $order->orderproducts->checkIncludedInCoupon($coupon);
            }

            $coupon = $order->coupon_info2;
            $coupon = Arr::get($coupon, 0);

            $fromWallet = min($invoiceInfo['price']['payableByWallet'], $credit);

            $redirectTo = $this->getEncryptedUrl('saman', 'android', encrypt(['user_id' => $user->id]));
            $redirectTo = str_replace('alaatv.com', 'admin.alaatv.com', $redirectTo);
            $invoiceInfo = array_merge($invoiceInfo, [
                'coupon' => $coupon,
                'referralCode' => $order->referralCode,
                'orderHasDonate' => $orderHasDonate,
                'redirectToGateway' => $redirectTo,
                'payByWallet' => $order->seller == config('constants.SOALAA_SELLER') ? null : $fromWallet,
            ]);
        } else {
            $cartItems = json_decode(json_encode($request->get('cartItems'))) ?? [];
            $fakeOrderproducts = OrderProductsService::convertOrderproductObjectsToCollection($cartItems);
            $invoiceInfo = (new AlaaInvoiceGenerator())->generateFakeOrderproductsInvoice($fakeOrderproducts);
        }

        return (new InvoiceResource($invoiceInfo))->response();
    }

    /**
     * Showing payment step in checkout the process
     *
     * @param  Request  $request
     *
     * @return Response
     * @throws Exception
     */
    public function checkoutPayment(Request $request)
    {
        $user = $request->user('api');
        /** @var Order $order */
        $order = $user->getOpenOrderOrCreate();

        $wallets = optional($order->user)->getWallet();
        $orderHasDonate = $order->hasTheseProducts([
            Product::CUSTOM_DONATE_PRODUCT,
            Product::DONATE_PRODUCT_5_HEZAR,
        ]);

        $coupon = $order->coupon;
        $couponValidationStatus = optional($coupon)->validateCoupon();
        if (in_array($couponValidationStatus, [
            Coupon::COUPON_VALIDATION_STATUS_DISABLED,
            Coupon::COUPON_VALIDATION_STATUS_USAGE_TIME_NOT_BEGUN,
            Coupon::COUPON_VALIDATION_STATUS_EXPIRED,
        ])) {
            $order->detachCoupon();
            if ($order->updateWithoutTimestamp()) {
                $coupon->decreaseUseNumber();
                $coupon->update();
            }

            $order = $order->fresh();
        }
        $coupon = $order->coupon_info;
        $notIncludedProductsInCoupon = $order->reviewCouponProducts();

        $invoiceGenerator = new AlaaInvoiceGenerator();
        $invoiceInfo = $invoiceGenerator->generateOrderInvoice($order);

        return response([
            'price' => $invoiceInfo['price'],
            'wallet' => $wallets,
            'couponInfo' => $coupon,
            'notIncludedProductsInCoupon' => $notIncludedProductsInCoupon,
            'orderHasDonate' => $orderHasDonate,
        ]);
    }

    /**
     * Submits a coupon for the order
     *
     * @param  SubmitCouponRequest  $request
     *
     * @param  AlaaInvoiceGenerator  $invoiceGenerator
     *
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function submitCoupon(SubmitCouponRequest $request, AlaaInvoiceGenerator $invoiceGenerator)
    {
        $coupon = CouponRepo::findCouponByCode($request->get('code'));
        $user = $request->user();
        if ($request->has('openOrder')) {
            $order = $request->get('openOrder');
        } else {
            $order = Order::Find($request->get('order_id'));
            if (!isset($order)) {
                return response($this->makeErrorResponse(Response::HTTP_BAD_REQUEST, 'Invalid order'));
            }
        }

        Cache::tags(['order_'.$order->id])->flush();

        if (!isset($coupon)) {
            return response($this->makeErrorResponse(Response::HTTP_BAD_REQUEST, 'Invalid coupon'));
        }

        if (!$this->canUserUseCoupon($coupon, $user)) {
            return response($this->makeErrorResponse(Response::HTTP_BAD_REQUEST,
                'You are not the owner of this coupon'));
        }

        $couponValidationStatus = $coupon->validateCoupon();
        if ($couponValidationStatus != Coupon::COUPON_VALIDATION_STATUS_OK) {
            return response($this->makeErrorResponse(Response::HTTP_BAD_REQUEST,
                Coupon::COUPON_VALIDATION_INTERPRETER[$couponValidationStatus] ?? 'Coupon validation status is undetermined'));
        }

        $result = (new CouponSubmitter($order))->submit($coupon);
        if ($result === true) {
            $invoiceGenerator->generateOrderInvoice($order);
            return response([
                $coupon,
                'message' => 'Coupon attached successfully',
            ]);
        }

        return response($this->makeErrorResponse(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error'));
    }

    /** API Version 2
     *
     * @param  SubmitCouponRequest  $request
     * @param  AlaaInvoiceGenerator  $invoiceGenerator
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function submitCouponV2(SubmitCouponRequest $request, AlaaInvoiceGenerator $invoiceGenerator)
    {
        /** @var User $user */
        $user = $request->user('api');

        $coupon = CouponRepo::findCouponByCode($request->get('code'));
        if (!isset($coupon)) {
            return myAbort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid coupon');
        }

        if ($coupon->hasPurchased) {
            $owner = $coupon->purchasedOrderproducts->first()?->order?->user;
            if (isset($owner) && $owner->id != $user->id) {
                return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Error on attaching coupon to order');
            }
        }

        if (!$this->canUserUseCoupon($coupon, $user)) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Your are not the owner');
        }

        $checkedCouponRequirement = $this->checkCouponRequirements($coupon, $user);
        $checkedCouponUnrequirement = $this->checkCouponUnrequirements($coupon, $user);
        if (!$checkedCouponRequirement || !$checkedCouponUnrequirement) {
            return response()->json([
                'error' => [
                    'message' => 'شما محصولات پیش نیاز را خریداری نکرده اید',
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        if ($request->has('openOrder')) {
            $order = $request->get('openOrder');
        } else {
            $order = Order::Find($request->get('order_id'));
            if (!isset($order)) {
                return myAbort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid order');
            }
        }

        Cache::tags(['order_'.$order->id])->flush();

        $couponValidationStatus = $coupon->validateCoupon();
        if ($couponValidationStatus != Coupon::COUPON_VALIDATION_STATUS_OK) {
            return myAbort(Response::HTTP_UNPROCESSABLE_ENTITY,
                Coupon::COUPON_VALIDATION_INTERPRETER[$couponValidationStatus] ?? 'Coupon validation status is undetermined');
        }
        $result = (new CouponSubmitter($order))->submit($coupon);
        if ($result !== true) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Error on attaching coupon to order');
        }
        $invoiceInfo = $invoiceGenerator->generateOrderInvoice($order);
        $priceInfo = $invoiceInfo['price'];
        $coupon = $order->fresh()->coupon_info2;
        $coupon = Arr::get($coupon, 0);

        $credit = $user->getTotalWalletBalance();
        $fromWallet = min($invoiceInfo['price']['payableByWallet'], $credit);

        $resource = [
            'priceInfo' => $priceInfo,
            'coupon' => $coupon,
            'payableByWallet' => $fromWallet,
        ];

        return (new CouponInfoResource($resource))->response();
    }

    /**
     * @param  Request  $request
     *
     * @return ResponseFactory|Response
     */
    public function removeCoupon(Request $request)
    {
        if ($request->has('openOrder')) {
            $order = $request->get('openOrder');
        } else {
            $order_id = $request->get('order_id');
            $order = Order::Find($order_id);
        }

        if (isset($order)) {
            Cache::tags(['order_'.$order->id,])->flush();

            $coupon = $order->coupon;
            if (isset($coupon)) {
                $order->detachCoupon();
                if ($order->updateWithoutTimestamp()) {
                    $coupon->decreaseUseNumber();
                    $coupon->update();
                    $resultCode = Response::HTTP_OK;
                    $resultText = 'Coupon detached successfully';
                } else {
                    $resultCode = Response::HTTP_SERVICE_UNAVAILABLE;
                    $resultText = 'Database error';
                }
            } else {
                $resultCode = Response::HTTP_BAD_REQUEST;
                $resultText = 'No coupon found for this order';
            }
        } else {
            $resultCode = Response::HTTP_BAD_REQUEST;
            $resultText = 'Unknown order';
        }

        if ($resultCode == Response::HTTP_OK) {
            $response = [
                'message' => 'Coupon detached successfully',
            ];
        } else {
            $response = [
                'error' => [
                    'code' => $resultCode ?? $resultCode,
                    'message' => $resultText ?? $resultText,
                ],
            ];
        }

        return response($response);
    }

    /**
     * @param  Request  $request
     *
     * @param  AlaaInvoiceGenerator  $invoiceGenerator
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function removeCouponV2(Request $request, AlaaInvoiceGenerator $invoiceGenerator)
    {
        $user = $request->user();
        if ($request->has('openOrder')) {
            $order = $request->get('openOrder');
        } else {
            $order_id = $request->get('order_id');
            $order = Order::Find($order_id);
        }

        if (isset($order)) {
            Cache::tags(['order_'.$order->id,])->flush();

            $coupon = $order->coupon;
            if (isset($coupon)) {
                $order->detachCoupon();
                if ($order->updateWithoutTimestamp()) {
                    $coupon->decreaseUseNumber();
                    $coupon->update();
                    $resultCode = Response::HTTP_OK;
                    $resultText = 'Coupon detached successfully';
                } else {
                    $resultCode = Response::HTTP_SERVICE_UNAVAILABLE;
                    $resultText = 'Database error';
                }
            } else {
                $resultCode = Response::HTTP_BAD_REQUEST;
                $resultText = 'No coupon found for this order';
            }
        } else {
            $resultCode = Response::HTTP_BAD_REQUEST;
            $resultText = 'Unknown order';
        }

        if ($resultCode != Response::HTTP_OK) {
            return myAbort($resultCode, $resultText);
        }
        $invoiceInfo = $invoiceGenerator->generateOrderInvoice($order);
        $priceInfo = $invoiceInfo['price'];

        $credit = $user->getTotalWalletBalance();
        $fromWallet = min($invoiceInfo['price']['payableByWallet'], $credit);

        $resource = [
            'priceInfo' => $priceInfo,
            'coupon' => null,
            'payableByWallet' => $fromWallet,
        ];

        return (new CouponInfoResource($resource))->response();
    }

    public function submitReferralCode(SubmitReferralCodeRequest $request, AlaaInvoiceGenerator $invoiceGenerator)
    {
        /** @var User $user */
        $user = $request->user();

        $referralCode = ReferralCode::where('code', $request->input('referral_code'))->first();

        if ($request->has('openOrder')) {
            $order = $request->get('openOrder');
        } else {
            $order = Order::Find($request->get('order_id'));
            if (!isset($order)) {
                return myAbort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Invalid order');
            }
        }

        Cache::tags(['order_'.$order->id])->flush();

        $referralCodeValidationStatus = $referralCode->validateReferralCode();
        if ($referralCodeValidationStatus != ReferralCode::REFERRAL_CODE_VALIDATION_STATUS_OK) {
            return myAbort(Response::HTTP_UNPROCESSABLE_ENTITY,
                ReferralCode::REFERRAL_CODE_VALIDATION_INTERPRETER[$referralCodeValidationStatus] ?? 'Referral code validation status is undetermined');
        }
        $result = (new ReferralCodeSubmitter($order))->submit($referralCode);
        if ($result !== true) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Error on attaching referral code to order');
        }
        $referralCode->update(['isAssigned' => 1,]);
        $invoiceInfo = $invoiceGenerator->generateOrderInvoice($order);
        $priceInfo = $invoiceInfo['price'];
        if ($priceInfo['final'] < config('constants.REFERRAL_CODE_USING_MIN_PRICE')) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE,
                'مبلغ نهایی سبد خرید باید بزرگتر از '.number_format(config('constants.REFERRAL_CODE_USING_MIN_PRICE')).' تومان باشد');
        }

        $credit = $user->getTotalWalletBalance();
        $fromWallet = min($invoiceInfo['price']['final'], $credit);
        $priceInfo['payableByWallet'] = $fromWallet;
        $resource = [
            'message' => 'Referral code attached successfully',
            'referralCode' => $referralCode,
            'priceInfo' => $priceInfo,
        ];

        return new ReferralCodeInfoWithPrice($resource);
    }

    public function removeReferralCode(Request $request, AlaaInvoiceGenerator $invoiceGenerator)
    {
        $user = $request->user();
        if ($request->has('openOrder')) {
            $order = $request->get('openOrder');
        } else {
            $order_id = $request->get('order_id');
            $order = Order::Find($order_id);
        }

        if (isset($order)) {
            Cache::tags(['order_'.$order->id,])->flush();

            $referralCode = $order->referralCode;
            if (isset($referralCode)) {
                $order->detachReferralCode();
                if ($order->updateWithoutTimestamp()) {
                    $referralCode->decreaseUseNumber();
                    $referralCode->update();
                    $resultCode = Response::HTTP_OK;
                    $resultText = 'referral code detached successfully';
                } else {
                    $resultCode = Response::HTTP_SERVICE_UNAVAILABLE;
                    $resultText = 'Database error';
                }
            } else {
                $resultCode = Response::HTTP_BAD_REQUEST;
                $resultText = 'No referral code found for this order';
            }
        } else {
            $resultCode = Response::HTTP_BAD_REQUEST;
            $resultText = 'Unknown order';
        }

        if ($resultCode != Response::HTTP_OK) {
            return myAbort($resultCode, $resultText);
        }
        $invoiceInfo = $invoiceGenerator->generateOrderInvoice($order);
        $priceInfo = $invoiceInfo['price'];
        $credit = $user->getTotalWalletBalance();
        $fromWallet = min($invoiceInfo['price']['final'], $credit);
        $priceInfo['payableByWallet'] = $fromWallet;
        $resource = [
            'message' => 'Referral code detached successfully',
            'referralCode' => $referralCode,
            'priceInfo' => $priceInfo,
        ];

        return new ReferralCodeInfoWithPrice($resource);
    }

    /**
     * Makes a donate request
     *
     * @param  DonateRequest  $request
     * @param  OrderproductController  $orderproductController
     *
     * @return RedirectResponse
     */
    public function donateOrder(DonateRequest $request)
    {
        $user = $request->user('alaatv');
        $amount = $request->get('amount');
        /** @var OrderCollections $donateOrders */
        $donateOrders = $user->orders->where('orderstatus_id', config('constants.ORDER_STATUS_OPEN_DONATE'));
        if ($donateOrders->isNotEmpty()) {
            $donateOrder = $donateOrders->first();
        } else {
            $donateOrder = Order::create([
                'orderstatus_id' => config('constants.ORDER_STATUS_OPEN_DONATE'),
                'paymentstatus_id' => config('constants.PAYMENT_STATUS_UNPAID'),
                'user_id' => $user->id,
            ]);
        }

        Cache::tags(['order_'.$donateOrder->id,])->flush();

        $donateProduct = Product::FindOrFail(Product::CUSTOM_DONATE_PRODUCT);

        $oldOrderproducts = $donateOrder->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->where('product_id', $donateProduct->id)
            ->get();

        if ($oldOrderproducts->isNotEmpty()) {
            $oldOrderproduct = $oldOrderproducts->first();
            $oldOrderproduct->cost = $amount;
            $oldOrderproduct->update();
        } else {
            $donateOrderproduct = Orderproduct::Create([
                'order_id' => $donateOrder->id,
                'product_id' => $donateProduct->id,
                'cost' => $amount,
                'orderproducttype_id' => config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
            ]);
        }

        $donateOrder = $donateOrder->fresh();
        $orderCost = $donateOrder->obtainOrderCost(true, false);
        $donateOrder->cost = $orderCost['rawCostWithDiscount'];
        $donateOrder->costwithoutcoupon = $orderCost['rawCostWithoutDiscount'];
        $donateOrder->update();

        return redirect()->route('api.v1.payment.getEncryptedLink', ['order_id' => $donateOrder->id]);
    }

    /**
     * API Version 2
     *
     * @param  DonateRequest  $request
     *
     * @return array
     */
    public function donateOrderV2(DonateRequest $request)
    {
        $user = $request->user('alaatv');
        if ($user === null) {
            abort(Response::HTTP_FORBIDDEN, 'Not authorized.');
        }
        $amount = $request->get('amount');
        /** @var OrderCollections $donateOrders */
        $donateOrders = $user->orders->where('orderstatus_id', config('constants.ORDER_STATUS_OPEN_DONATE'));
        if ($donateOrders->isNotEmpty()) {
            $donateOrder = $donateOrders->first();
        } else {
            $donateOrder = Order::create([
                'orderstatus_id' => config('constants.ORDER_STATUS_OPEN_DONATE'),
                'paymentstatus_id' => config('constants.PAYMENT_STATUS_UNPAID'),
                'user_id' => $user->id,
            ]);
        }

        Cache::tags(['order_'.$donateOrder->id,])->flush();

        $donateProduct = Product::FindOrFail(Product::CUSTOM_DONATE_PRODUCT);

        $oldOrderproducts = $donateOrder->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->where('product_id', $donateProduct->id)
            ->get();

        if ($oldOrderproducts->isNotEmpty()) {
            $oldOrderproduct = $oldOrderproducts->first();
            $oldOrderproduct->cost = $amount;
            $oldOrderproduct->update();
        } else {
            $donateOrderproduct = Orderproduct::Create([
                'order_id' => $donateOrder->id,
                'product_id' => $donateProduct->id,
                'cost' => $amount,
                'orderproducttype_id' => config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
            ]);
        }

        $donateOrder = $donateOrder->fresh();
        $orderCost = $donateOrder->obtainOrderCost(true, false);
        $donateOrder->cost = $orderCost['rawCostWithDiscount'];
        $donateOrder->costwithoutcoupon = $orderCost['rawCostWithoutDiscount'];
        $donateOrder->update();

        $paymentMethod = 'mellat';
        $device = $request->ajax() ? 'web' : 'android';
        $encryptedPostfix = encrypt(['user_id' => $user->id, 'order_id' => $donateOrder->id]);
        return [
            'data' => [
                'url' => $this->getEncryptedUrl($paymentMethod, $device, $encryptedPostfix),
            ],
        ];
    }

    /**
     * Adds a product to intended order
     *
     * @param  Request  $request
     * @param  Product  $product
     *
     * @return ResponseFactory|JsonResponse|Response
     */
    public function addDonate(Request $request)
    {
        try {
            /** @var User $user */
            $restored = false;
            $user = $request->user();
            $openOrder = $user->getOpenOrderOrCreate();
            Cache::tags(['order_'.$openOrder->id,])->flush();

            $donate_5_hezar = Product::DONATE_PRODUCT_5_HEZAR;
            $deletedOrderproduct = $openOrder->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
                ->where('product_id', $donate_5_hezar)
                ->onlyTrashed()
                ->first();
            if (isset($deletedOrderproduct)) {
                $deletedOrderproduct->restore();
                $restored = true;
            }

            if (!$restored) {
                $result = $this->new([
                    'product_id' => $donate_5_hezar,
                    'order_id' => $openOrder->id,
                    'withoutBon' => true,
                ]);
                if (!$result['status']) {
                    //ToDo : change the output of the method called new() in OrderproductController then fix this

                    $text = Arr::get(Arr::get($result, 'message'), 0);
                    if ($text !== 'This product has been added to order before') {
                        return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $text);
                    }

                }
            }

            /** @var Order $order */
            $order = $user->getOpenOrderOrCreate()->fresh();
            $credit = $user->getTotalWalletBalance();
            $invoiceInfo = (new AlaaInvoiceGenerator())->generateOrderInvoice($order);
            $fromWallet = min($invoiceInfo['price']['payableByWallet'], $credit);

            $invoiceInfo = array_merge($invoiceInfo, [
                'payByWallet' => $fromWallet,
            ]);

            return (new InvoicePriceResource($invoiceInfo))->response();

        } catch (Exception    $e) {
            return myAbort(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    public function removeDonate(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $openOrder = $user->getOpenOrderOrCreate();
        Cache::tags(['order_'.$openOrder->id,])->flush();
        $donate_5_hezar = Product::DONATE_PRODUCT_5_HEZAR;
        /** @var OrderproductCollection $orderproducts */
        $orderproducts = $openOrder->orderproducts(config('constants.ORDER_PRODUCT_TYPE_DEFAULT'))
            ->where('product_id', $donate_5_hezar)
            ->get();

        foreach ($orderproducts as $orderproduct) {
            $orderproduct->delete();
        }

        /** @var Order $order */
        $order = $user->getOpenOrderOrCreate();
        $credit = $user->getTotalWalletBalance();
        $invoiceInfo = (new AlaaInvoiceGenerator())->generateOrderInvoice($order);
        $fromWallet = min($invoiceInfo['price']['payableByWallet'], $credit);

        $invoiceInfo = array_merge($invoiceInfo, [
            'payByWallet' => $fromWallet,
        ]);

        return (new InvoicePriceResource($invoiceInfo))->response();
    }

    public function create3aOrder(CreateFreeOrderFor3aRequest $request)
    {
        $examId = $request->get('exam_id');
        $user = $request->user();
        Log::channel('register3AParticipantsErrors')->debug("User {$user->id} has requested for 3a exam {$examId} ");
        /** @var Collection $products */
        $products = _3aExam::where('id', $examId)->get();
        $productIds = $products->pluck('product_id')->toArray();
        if ($products->isEmpty()) {
            return response()->json(['Product not found'], Response::HTTP_BAD_REQUEST);
        }

//        if (!$products->enable) {
//            return response()->json(['Product is not enable'], Response::HTTP_BAD_REQUEST);
//        }

        if ($user->userHasAnyOfTheseProducts2($productIds)) {
            return response()->json(['success']);
        }

        Log::channel('register3AParticipantsErrors')->debug("User {$user->id} did not have product of this exam");

        $productId = $this->hasFreeStatus($products, $user);
        if ($productId) {
            $order = OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_PAID'), 0, 0);
            OrderproductRepo::createGiftOrderproduct($order->id, $productId, 0);

//            if (in_array($productId, array_keys(_3aExamNotification2::VALID_PRODUCTS))) {
//                $user->notify(new _3aExamNotification2($productId));
//            }
            Log::channel('register3AParticipantsErrors')->debug("User {$user->id} got this exam for free");
            CacheFlush::flushAssetCache($user);
            return response()->json(['success']);
        }

        if (!empty(array_intersect($productIds, [952, 953, 954, 955]))) {
            return response()->json(['You are not allowed to register in this exam'], Response::HTTP_FORBIDDEN);
        }

        $open3aOrder = $user->orders()->where('orderstatus_id', config('constants.ORDER_STATUS_OPEN_3A'))->first();
        if (!isset($open3aOrder)) {
            $open3aOrder = Order::create([
                'orderstatus_id' => config('constants.ORDER_STATUS_OPEN_3A'),
                'paymentstatus_id' => config('constants.PAYMENT_STATUS_UNPAID'),
                'user_id' => $user->id,
            ]);
        }

        Cache::tags(['order_'.$open3aOrder->id,])->flush();

        foreach ($open3aOrder->orderproducts as $oldOrderproduct) {
            $oldOrderproduct->delete();
        }

        $product = $products->sortBy('basePrice')->first();
        Orderproduct::Create([
            'order_id' => $open3aOrder->id,
            'product_id' => $product->id,
            'discountPercentage' => $product->discount,
            'cost' => $product->basePrice,
            'orderproducttype_id' => config('constants.ORDER_PRODUCT_TYPE_DEFAULT'),
        ]);

        $open3aOrder = $open3aOrder->fresh();
        $orderCost = $open3aOrder->obtainOrderCost(true, false);
        $open3aOrder->cost = $orderCost['rawCostWithDiscount'];
        $open3aOrder->costwithoutcoupon = $orderCost['rawCostWithoutDiscount'];
        $open3aOrder->save();

        CacheFlush::flushAssetCache($user);
        Log::channel('register3AParticipantsErrors')->debug("User {$user->id} has been redirected to gateway");
        return response()->json([
            'data' => [
                'redirect_url' => $this->getEncryptedUrl('saman2', 'web',
                    encrypt(['user_id' => $user->id, 'order_id' => $open3aOrder->id])),
            ],
        ], HTTPResponse::HTTP_FOUND);
    }

    private function hasFreeStatus(Collection $products, User $user)
    {
        $foundProduct = 0;
        foreach ($products as $product) {
            $hasAbrisham = $user->userHasAnyOfTheseProducts(array_keys(Product::ALL_ABRISHAM_PRODUCTS));
            $hasAbrishamPro =
                $user->userHasAnyOfTheseProducts(array_keys(Product::ALL_ABRISHAM_PRO_PRODUCTS));
            $hasTaftan =
                $user->userHasAnyOfTheseProducts([
                    Product::TAFTAN1401_TAJROBI_PACKAGE, Product::TAFTAN1401_RIYAZI_PACKAGE
                ]);
            $isMianTerm1Jambandi =
                in_array($product->product_id, [
                    Product::_3A_JAMBANDI_YAZDAHOM_TAJROBI_MIAN_TERM1_1401,
                    Product::_3A_JAMBANDI_YAZDAHOM_RIYAZI_MIAN_TERM1_1401,
                    Product::_3A_JAMBANDI_YAZDAHOM_ENSANI_MIAN_TERM1_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_TAJROBI_MIAN_TERM1_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_RIYAZI_MIAN_TERM1_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_ENSANI_MIAN_TERM1_1401
                ]);
            $isTem1Jambandi =
                in_array($product->product_id, [
                    Product::_3A_JAMBANDI_DAVAZDAHOM_RIYAZI_TERM1_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_ENSANI_TERM1_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_TAJROBI_TERM1_1401
                ]);
            $isJambandiPaye = in_array($product->product_id, [
                Product::_3A_DAVAZDAHOM_ENSANI_JAMBADNI_PAYE_BA_JOGHRAFI_DAHOM,
                Product::_3A_DAVAZDAHOM_ENSANI_JAMBADNI_PAYE_BA_JOGHRAFI_YAZDAHOM,
                Product::_3A_DAVAZDAHOM_RIYAZI_JAMBANDI_PAYE_BA_FIZIK_DAHOM,
                Product::_3A_DAVAZDAHOM_RIYAZI_JAMBANDI_PAYE_BA_FIZIK_YAZDAHOM,
                Product::_3A_DAVAZDAHOM_TAJROBI_JAMBANDI_PAYE_BA_FIZIK_DAHOM,
                Product::_3A_DAVAZDAHOM_TAJROBI_JAMBANDI_PAYE_BA_FIZIK_YAZDAHOM,
            ]);
            $isMianTerm2Jambandi =
                in_array($product->product_id, [
                    Product::_3A_JAMBANDI_YAZDAHOM_ENSANI_MIAN_TERM2_1401,
                    Product::_3A_JAMBANDI_YAZDAHOM_RIYAZI_MIAN_TERM2_1401,
                    Product::_3A_JAMBANDI_YAZDAHOM_TAJROBI_MIAN_TERM2_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_TAJROBI_MIAN_TERM1_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_RIYAZI_MIAN_TERM1_1401,
                    Product::_3A_JAMBANDI_DAVAZDAHOM_ENSANI_MIAN_TERM1_1401
                ]);
            $isJambandiDahomYazdahom =
                in_array($product->product_id, [
                    Product::_3A_DAVAZDAHOM_RIYAZI_JAMBANDI_DAHOM_VA_YAZDAHOM,
                    Product::_3A_DAVAZDAHOM_ENSANI_JAMBANDI_DAHOM_VA_YAZDAHOM,
                    Product::_3A_DAVAZDAHOM_TAJROBI_JAMBANDI_DAHOM_VA_YAZDAHOM, Product::TAFTAN1401_RIYAZI_PACKAGE,
                    Product::TAFTAN1401_TAJROBI_PACKAGE
                ]);
            $isJambandiOrdibehesht1401 = in_array($product->product_id, [680, 681, 682, 673, 672, 671,]);
            $isJambandiKhordad1401 = in_array($product->product_id, [730, 729, 728, 679, 678, 677, 676, 675, 674]);
            $isTaaiSath = in_array($product->product_id, [952, 953, 954, 955]);

            $specialConditionAlaa =
                (($hasAbrisham || $hasTaftan) && ($isMianTerm1Jambandi || $isTem1Jambandi || $isJambandiPaye || $isMianTerm2Jambandi || $isJambandiDahomYazdahom || $isJambandiOrdibehesht1401 || $isJambandiKhordad1401));

            $abrishamProCondition = $hasAbrishamPro & $isTaaiSath;

            $flag =
                $product->isFree ||
                ($product->basePrice == 0) ||
                $product->discount == 100 ||
                $specialConditionAlaa ||
                $abrishamProCondition ||
                $user->isDeveloper() ||
                $user->hasRole(config('constants.ROLE_3A_MANAGER'));

            if ($flag) {
                $foundProduct = $product->product_id;
                break;
            }
        }
        return $foundProduct;
    }

    public function storeFree(InsertFreeOrderRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        $data = $request->validated();

        // Note : At fist I had decided to accept an array of product ids as input .
        //But then I came to this conclusion that it was a mistake and since I did not want to make a change in UI ,
        // I decided to convert the input array to an individual product . This is not going to make any problems because the UI that is using it currently always sends one product at a time
        /** @var Collection $products */
        $products = ProductRepository::getProductsById([Arr::get($data, 'products')])->get();
        /** @var Product $product */
        if (!$products->first()->isFree() && $products->first()->basePrice != 0) {
            return myAbort(Response::HTTP_FORBIDDEN, 'محصول انتخاب شده رایگان نمی باشد');
        }

        if (!$products->first()->isEnableToPurchase()) {
            return myAbort(Response::HTTP_FORBIDDEN, 'محصول انتخاب شده فعال نمی باشد');
        }

        if ($user->getPurchasedOrderproduct($products->first()->id)) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'شما از محصول انتخاب شده قبلا خریداری کرده اید');
        }

        $order = $this->addProductsToAsset(collect($products), $user);
        event(new UserPurchaseCompleted($order));
        event(new SendOrderNotificationsEvent($order, $user));

        return response()->json([
            'data' => [
                'gtmEec' => (new GtmEec())->generateGtmEec($order->id, 'web', $order->totalCost()),
            ],
        ]);
    }

    public function show(Order $order)
    {
        $user = auth('api')->user();

        if ($user->id == $order->user_id) {
            return new \App\Http\Resources\Order($order);
        }
        return response()->json(myAbort(401, 'unauthorized'));
    }

    public function freeSubscription(UserSubscriptionRequest $request)
    {
        $user = $request->user();

        $product = Product::findOrFail($request->get('subscription_id'));


        if ($response = $product->validateProduct()) {
            return response()->json(['message' => $response], Response::HTTP_BAD_REQUEST);
        }
        if (!$product->isFree()) {
            return response()->json(['message' => 'اشتراک رایگان نیست'], Response::HTTP_BAD_REQUEST);
        }
        Cache::tags(['userAsset', 'userAsset_'.$user->id])->flush();
        if ($user->userHasAnyOfTheseProducts2([$product->id])) {
            return response()->json(['message' => 'این اشتراک قبلا استفاده شده است.'], Response::HTTP_BAD_REQUEST);
        }

        $features = $product->attributevaluesByType(config('constants.ATTRIBUTE_TYPE_SUBSCRIPTION'))
            ->load('attribute')
            ->pluck('name', 'attribute.name');

        $features->each(function ($value, $key) use (&$values) {
            $values[] = [
                'title' => $key,
                'usageLimit' => $value,
                'usage' => $value == config('constants.ATTRIBUTE_VALUE_INFINITE') ? config('constants.ATTRIBUTE_VALUE_INFINITE') : 0,
            ];
        });

        try {
            DB::beginTransaction();
            $order = OrderRepo::createBasicCompletedOrder($user->id, config('constants.PAYMENT_STATUS_PAID'), 0, 0,
                seller: config('constants.SOALAA_SELLER'));
            OrderproductRepo::createBasicOrderproduct($order->id, $product->id, 0, 0);
            $user->subscribedProducts()->attach($product->id, [
                'order_id' => $order->id,
                'seller' => $product->seller,
                'values' => json_encode($values),
                'valid_since' => Carbon::now(),
                'valid_until' => isset($features['duration']) ? Carbon::now()->addDays((int) $features['duration']) : Carbon::now(),
                'created_at' => Carbon::now(),
            ]);
            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'error' => 'خطایی رخ داده است',
            ], HTTPResponse::HTTP_INTERNAL_SERVER_ERROR);

        }

        return response()->json([
            'message' => 'اشتراک رایگان ثبت شد',
        ], HTTPResponse::HTTP_CREATED);
    }

    public function removeOrderProduct(Product $product, OrderProductsService $orderProductsService)
    {
        $user = auth('api')->user();
        $openOrder = $user->getOpenOrderOrCreate();
        $orderProduct = $openOrder->orderproducts->where('product_id', $product->id)->first();
        if ($orderProduct) {
            $orderProductsService->destroyOrderProduct($orderProduct);
        }
        return response()->json([
            'status' => 'product deleted from cart successfully',
        ]);
    }

    public function exchangeOrderproduct(Order $order, Request $request, TransactionController $transactionController)
    {
        $done = false;
        $data = [];

        $exchangeArray1 = $request->get('exchange-a');
        foreach ($exchangeArray1 as $key => $item) {
            $newProduct = Product::where('id', $item['orderproductExchangeNewProduct'])
                ->first();
            if (isset($newProduct)) {
                $done = true;
                $orderproduct = Orderproduct::where('id', $key)
                    ->first();
                if ($orderproduct->order_id != $order->id) {
                    continue;
                }
                if (isset($orderproduct)) {
                    $orderproduct->product_id = $newProduct->id;
                    if (strlen(trim($item['orderproductExchangeNewCost'])) > 0) {
                        $orderproduct->cost = $item['orderproductExchangeNewCost'];
                    }
                    if (strlen(trim($item['orderproductExchangeNewDiscountAmount'])) > 0) {
                        $orderproduct->discountAmount = $item['orderproductExchangeNewDiscountAmount'];
                    }
                    $orderproduct->discountPercentage = 0;
                    $orderproduct->includedInCoupon = 0;
                    $orderproduct->save();
                    $data[] = $orderproduct;
                }
            }
        }

        $exchangeArray2 = $request->get('exchange-b');
        foreach ($exchangeArray2 as $item) {
            $newProduct = Product::where('id', $item['newOrderproductProduct'])
                ->first();
            if (isset($newProduct)) {
                $done = true;
                $orderproduct = new Orderproduct();
                $orderproduct->product_id = $newProduct->id;
                $orderproduct->order_id = $order->id;
                if (strlen(trim($item['neworderproductCost'])) > 0) {
                    $orderproduct->cost = $item['neworderproductCost'];
                }
                $orderproduct->save();
                $data[] = $orderproduct;
            }
        }

        if ($request->has('orderproductExchangeTransacctionCheckbox')) {
            $done = true;
            $request->merge(['order_id' => $order->id]);
            $transactionRequest = $request->only([
                'order_id', 'cost', 'traceNumber', 'referenceNumber', 'paycheckNumber', 'managerComment',
                'paymentmethod_id', 'transactionstatus_id'
            ]);
            $transactionController->store($transactionRequest);
        }

        if (!$done) {
            return response()->json(['message' => 'No operation was performed'], HTTPResponse::HTTP_BAD_REQUEST);
        }

        $newOrder = Order::where('id', $order->id)
            ->first();
        if ($newOrder) {
            $orderCost = $newOrder->obtainOrderCost(true, false, 'REOBTAIN');
            $newOrder->cost = $orderCost['rawCostWithDiscount'];
            $newOrder->costwithoutcoupon = $orderCost['rawCostWithoutDiscount'];
            $newOrder->save();
            $data[] = $newOrder;
        } else {
            return response()->json(['message' => 'Failed to update order price'],
                HTTPResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['data' => $data, 'message' => 'Items exchanged successfully'], HTTPResponse::HTTP_OK);
    }

}

