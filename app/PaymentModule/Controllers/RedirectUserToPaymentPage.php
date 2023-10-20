<?php

namespace App\PaymentModule\Controllers;


use App\Classes\Payment\RefinementRequest\RefinementLauncher;
use App\Events\UserRedirectedToPayment;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Requests\RedirectToPaymentRequest;
use App\Jobs\CheckCouponOfUnpaidOrder;
use App\Jobs\CheckSubscriptionOrderproductOfUnpaidOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Transactiongateway;
use App\Models\User;
use App\PaymentModule\Money;
use App\PaymentModule\Repositories\OrdersRepo;
use App\PaymentModule\Responses;
use App\Repositories\TransactionGatewayRepo;
use App\Repositories\TransactionRepo;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class RedirectUserToPaymentPage extends Controller
{
    /**
     * redirect the user to online payment page
     *
     * @param  string  $paymentMethod
     * @param  string  $device
     *
     * @param  RedirectToPaymentRequest  $request
     *
     * @return Application|Factory|JsonResponse|View
     */
    public function __invoke(string $paymentMethod, string $device, RedirectToPaymentRequest $request)
    {

        $data = $this->getRefinementData($request->all(), $request->user());

        /** @var User $user */
        $user = $data['user'];
        /** @var Order $order */
        $order = $data['order'];
        if (is_null($order)) {
            Log::error("In RedirectUserToPaymentPage : order of user was not found : {$user?->id}");
            return response()->json('Cart is empty');
        }
        $seller = $order->seller;
        $gateway = $this->getMyGateway($paymentMethod, $seller);
        if (!$gateway) {
            return response()->json('Gateway is disabled');
        }

        /** @var User $authUser */
        $authUser = $request->user();
        if (!isset($authUser)) {
            return view('order.checkout.gatewayRedirect');
        }


        if ($data['statusCode'] != Response::HTTP_OK) {
            return $this->sendErrorResponse($data['message'] ?: '',
                $data['statusCode'] ?: Response::HTTP_SERVICE_UNAVAILABLE);
        }

        /** @var Order $order */
        $orderUniqueId = $data['orderUniqueId'];
        /** @var Money $cost */
        $cost = Money::fromTomans((int) $data['cost']);
        /** @var Transaction $transaction */
        $transaction = $data['transaction'];

        if (isset($order) && $order->user_id != $authUser->id) {
            auth()->logout();
            $loginMessage = 'سفارش مورد نظر با اکانتی که با آن لاگین بودید ثبت نشده بود. لطفا با اکانت صاحب سفارش لاگین کنید.';
            return view('order.checkout.gatewayRedirect', compact('authUser', 'loginMessage'));
        }

        $customerDescription = $request->get('customerDescription');

        $this->shouldGoToOfflinePayment($cost->tomans(), $user)
            ->thenRespondWith([
                [Responses::class, 'sendToOfflinePaymentProcess'], [$device, $order, $customerDescription]
            ]);

        /** @var string $description */
        $description = $this->getTransactionDescription($data['description'], $device, $user->mobile, $order);

        // Start going to gateway
        config()->set('payment', config('payment'));
        config()->set('payment.default', $gateway->name);
        config()->set("payment.drivers.$paymentMethod.callbackUrl",
            $this->comeBackFromGateWayUrl($paymentMethod, $device));
        $invoice = (new Invoice())->amount($cost->tomans())->detail(['description' => $description])->via($paymentMethod);
        try {

            $providing = Payment::purchase(
                $invoice,
                function ($driver, $transactionId) use (
                    $description,
                    $authUser,
                    $customerDescription,
                    $order,
                    $device,
                    $transaction,
                    $paymentMethod,
                    $gateway,
                    $invoice
                ) {
                    TransactionRepo::setAuthorityForTransaction($transactionId, $transaction->id, $gateway->id,
                        $description, $device, $invoice->getUuid())
                        ->orRespondWith([Responses::class, 'editTransactionError']);

                    if ($this->shouldCloseOrder($order)) {
                        OrdersRepo::closeOrder($order->id, ['customerDescription' => $customerDescription]);
                        $this->saveOrderInCookie($order);
                    }

                    if (isset($order->coupon) && optional($order->coupon)->hasPurchased) {
                        dispatch(new CheckCouponOfUnpaidOrder($transaction))->delay(now('Asia/Tehran')->addMinutes(20));
                    }

                    $hasOrderSubscription = $order->hasOrderproductViaSubsctiption();
                    if ($hasOrderSubscription) {
                        dispatch(new CheckSubscriptionOrderproductOfUnpaidOrder($authUser,
                            $order))->delay(now('Asia/Tehran')->addMinutes(20));
                    }
                }
            );

            event(new UserRedirectedToPayment($user));
            return $providing->pay()->render();
        } catch (Exception $exception) {
            Log::error($exception->getMessage().' - '.$exception->getFile().' : '.$exception->getLine());
            return view('errors.errorPage',
                ['message' => 'متاسفانه در حال حاضر درگاه بانکی دچار اختلال شده است ، از شکیبایی شما متشکریم']);
        }
    }

    /**
     * @param $inputData
     * @param $user
     *
     * @return array
     */
    private function getRefinementData($inputData, $user): array
    {
        $inputData['transactionController'] = app(TransactionController::class);
        $inputData['user'] = $user;

        return (new RefinementLauncher($inputData))->getData($inputData);
    }

    private function getMyGateway(string $paymentMethod, ?int $seller): ?Transactiongateway
    {
        if ($seller == config('constants.SOALAA_SELLER')) {
            return $this->findMyGateway('saman2');
        }

        if ($seller == config('constants.ALAA_SELLER') && $paymentMethod == 'saman2') {
            return $this->findMyGateway('saman');
        }

        if ($paymentMethod == 'random') {
            return TransactionGatewayRepo::getRandomGateway()->first();
        }

        $gateway = $this->findMyGateway($paymentMethod);
        if ($gateway->enable) {
            return $gateway;
        }

        return TransactionGatewayRepo::getTransactionGateways(['enable' => 1])->first();
//       return TransactionGatewayRepo::getRandomGateway()->first();
    }

    private function findMyGateway(string $gateway)
    {
        return TransactionGatewayRepo::getTransactionGatewayByName($gateway)
            ->orFailWith([Responses::class, 'sendErrorResponse'],
                ['msg' => 'No DB record found for this gateway', Response::HTTP_BAD_REQUEST]);
    }

    /**
     * @param  string  $msg
     * @param  int  $statusCode
     *
     * @return JsonResponse
     */
    private function sendErrorResponse(string $msg, int $statusCode)
    {
        return response()->json(['message' => $msg], $statusCode);
    }

    /**
     * @param  int  $cost
     *
     * @return Boolean
     */
    private function shouldGoToOfflinePayment(int $cost, $user)
    {
        return boolean($cost <= 0);
    }

    /**
     * @param  string  $description
     * @param  string  $device
     * @param              $mobile
     * @param  Order|null  $order
     *
     * @return string
     */
    private function getTransactionDescription(string $description, string $device, $mobile, $order = null)
    {
        $description = '';
        if ($device == 'web') {
            $description .= 'سایت آلاء - ';
        } else {
            if ($device == 'android') {
                $description .= 'اپ اندروید آلاء - ';
            }
        }
        $description .= $mobile.' - محصولات: ';

        if (is_null($order)) {
            return $description;
        }

        $order->orderproducts->load('product');

        foreach ($order->orderproducts as $orderProduct) {
            if (isset($orderProduct->product->id)) {
                $description .= $orderProduct->product->name.' , ';
            } else {
                $description .= 'یک محصول نامشخص , ';
            }
        }

        return $description;
    }

    /**
     * @param  string  $paymentMethod
     * @param  string  $device
     *
     * @return string
     */
    private function comeBackFromGateWayUrl(string $paymentMethod, string $device)
    {
        return route('verifyOnlinePayment',
            ['paymentMethod' => $paymentMethod, 'device' => $device, '_token' => csrf_token()]);
    }

    /**
     * @param  Order  $order
     *
     * @return bool
     */
    private function shouldCloseOrder(Order $order): bool
    {
        return $order->orderstatus_id == config('constants.ORDER_STATUS_OPEN');
    }

    /**
     * Saves order in cookie
     *
     * @param  Order  $order
     */
    private function saveOrderInCookie(Order $order)
    {
        $orderproducts = $order->orderproducts;

        $totalCookie = $this->handleOrders($orderproducts);

        if (!$totalCookie->isNotEmpty()) {
            return null;
        }
        setcookie('cartItems', $totalCookie->toJson(), time() + 3600, '/');
    }

    /**
     * @param $orderproducts
     *
     * @return Collection
     */
    private function handleOrders(Collection $orderproducts)
    {
        $totalCookie = collect();
        foreach ($orderproducts as $orderproduct) {
            $extraAttributesIds = $orderproduct->attributevalues->pluck('id')->toArray();
            $myProduct = $orderproduct->product;

            $grandProduct = $myProduct->grand;
            if (is_null($grandProduct)) {
                $totalCookie->push([
                    'product_id' => $myProduct->id,
                    'products' => [],
                    'extraAttribute' => $extraAttributesIds,
                ]);
                continue;
            }

            $grandType = $grandProduct->producttype_id;
            if ($grandType == config('constants.PRODUCT_TYPE_SELECTABLE')) {
                $this->makeCookieForSelectableGrand($totalCookie, $grandProduct, $myProduct, $extraAttributesIds);
            } else {
                if ($grandType == config('constants.PRODUCT_TYPE_CONFIGURABLE')) {
                    $this->makeCookieForConfigurableGrand($totalCookie, $myProduct, $grandProduct, $extraAttributesIds);
                }
            }

        }

        return $totalCookie;
    }

    /**
     * @param  Collection  $totalCookie
     * @param            $grandProduct
     * @param            $myProduct
     * @param            $extraAttributesIds
     */
    private function makeCookieForSelectableGrand(
        Collection $totalCookie,
        Product $grandProduct,
        Product $myProduct,
        array $extraAttributesIds
    ): void {
        $isAdded = $totalCookie->where('product_id', $grandProduct->id);
        if ($isAdded->isEmpty()) {
            $totalCookie->push([
                'product_id' => $grandProduct->id,
                'products' => [$myProduct->id],
                'extraAttribute' => $extraAttributesIds,
            ]);
        } else {
            $key = $isAdded->keys()->last();
            $addedCookie = $isAdded->first();
            $addedCookie['products'] = array_merge_recursive($addedCookie['products'], [$myProduct->id]);
            $addedCookie['extraAttribute'] = array_merge_recursive($addedCookie['extraAttribute'], $extraAttributesIds);
            $totalCookie->put($key, $addedCookie);
        }
    }

    /**
     * @param  Collection  $totalCookie
     * @param            $myProduct
     * @param            $grandProduct
     * @param            $extraAttributesIds
     */
    private function makeCookieForConfigurableGrand(
        Collection $totalCookie,
        Product $myProduct,
        Product $grandProduct,
        array $extraAttributesIds
    ): void {
        $attributeValueIds = $this->getProductAttributes($myProduct);
        if (empty($attributeValueIds)) {
            return;
        }
        $totalCookie->push([
            'product_id' => $grandProduct->id,
            'attribute' => $attributeValueIds,
            'extraAttribute' => $extraAttributesIds,
        ]);
    }

    /**
     * @param $myProduct
     *
     * @return mixed
     */
    private function getProductAttributes(Product $myProduct)
    {
        return $myProduct->attributevalues()->whereHas('attribute', function ($q) {
            $q->where('attributetype_id', config('constants.ATTRIBUTE_TYPE_MAIN'));
        })->get()->pluck('id')->toArray();
    }
}
