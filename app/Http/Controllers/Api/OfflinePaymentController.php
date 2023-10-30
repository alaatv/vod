<?php

namespace App\Http\Controllers\Api;

use App\Events\SendOrderNotificationsEvent;
use App\Events\UserPurchaseCompleted;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Traits\APIRequestCommon;
use App\Traits\OrderCommon;
use App\Traits\User\AssetTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OfflinePaymentController extends Controller
{

    use OrderCommon;
    use AssetTrait;
    use APIRequestCommon;

    /**
     * OfflinePaymentController constructor.
     *
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {

    }

    /**
     * @param  Request  $request
     * @param  string  $paymentMethod
     * @param  string  $device
     *
     * @return JsonResponse
     */
    public function verifyPayment(Request $request, string $paymentMethod, string $device): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'No User Found'], ResponseAlias::HTTP_EXPECTATION_FAILED);
        }

        $customerDescription = $request->session()->get('customerDescription');

        $getOrder = $this->getOrder($request, $user);
        if ($getOrder['error']) {
            return response()->json($getOrder['text'], $getOrder['httpStatusCode']);
        }

        $order = $getOrder['data']['order'];

        $check = $this->checkOrder($order, $user);
        if ($check['error']) {
            return response()->json($check['text'], $check['httpStatusCode']);
        }

        if (!$this->processVerification($order, $paymentMethod, $customerDescription, $user)) {
            return response()->json(['message' => 'Invalid inputs'], ResponseAlias::HTTP_BAD_REQUEST);
        }

        $responseMessages = $this->makeResponseMessage($order);

        $request->session()->flash('verifyResult', [
            'messages' => $responseMessages,
            'cardPanMask' => null,
            'RefID' => null,
            'isCanceled' => false,
            'orderId' => $order->id,
            'paidPrice' => 1,
        ]);

        event(new UserPurchaseCompleted($order));

        if ($order->seller == config('constants.SOALAA_SELLER')) {
            return response()->json(['redirect_url' => 'https://soalaa.com/order/'.$order->id.'/thankYou']);
        }

        return response()->json(['redirect_url' => config('constants.APP_URL').'/panel/order/'.$order->id.'/thankYou']);
    }

    /**
     * @param  Request  $request
     *
     * @param  User  $user
     *
     * @return array
     */
    private function getOrder(Request $request, User $user): array
    {
        if ($request->has('coi')) {
            $order = Order::Find($request->coi);
        } else {
            $order = $user->openOrders->first();
        }

        $error = false;
        $response = ResponseAlias::HTTP_OK;
        if (!isset($order)) {
            $error = true;
            $response = Response::HTTP_BAD_REQUEST;
            $text = 'No order found';
        }

        return [
            'error' => $error,
            'httpStatusCode' => $response,
            'text' => $text ?? '',
            'data' => [
                'order' => $order ?? null,
            ],
        ];
    }

    private function checkOrder(Order $order, User $user): array
    {
        $result = [
            'error' => false,
        ];
        if (isset($order)) {
            if (!$order->doesBelongToThisUser($user)) {
                $result = [
                    'error' => true,
                    'httpStatusCode' => ResponseAlias::HTTP_UNAUTHORIZED,
                    'text' => "Order doesn't belong to you",
                ];
            }
        } else {
            $result = [
                'error' => true,
                'httpStatusCode' => ResponseAlias::HTTP_NOT_FOUND,
                'text' => 'Order not found',
            ];
        }

        return $result;
    }

    /**
     * @param  Order  $order
     * @param  string  $paymentMethod
     * @param  string|null  $customerDescription
     *
     * @param  User  $user
     *
     * @return bool
     */
    private function processVerification(
        Order $order,
        string $paymentMethod,
        string $customerDescription = null,
        User $user
    ): bool {
        $done = true;
        switch ($paymentMethod) {
            case 'inPersonPayment' :
            case 'offlinePayment':
                $order->close(config('constants.PAYMENT_STATUS_UNPAID'));

                break;
            case 'wallet':
            case 'noPayment':

                /** Wallet transactions */
//                $order->closeWalletPendingTransactions();
                $wallets = optional($order->user)->wallets;
                if (isset($wallets)) {
                    $this->withdrawWalletPendings($order->id, $wallets);
                }

                $order = $order->fresh();
                /** End */

                $order->orderstatus_id = config('constants.ORDER_STATUS_CLOSED');
                $order->completed_at = Carbon::now('Asia/Tehran');
                if ($order->hasCost()) {
                    $cost = $order->totalCost() - $order->totalPaidCost();
                    if ($cost == 0) {

                        $order->paymentstatus_id = config('constants.PAYMENT_STATUS_PAID');
                        if (strlen($customerDescription) > 0) {
                            $order->customerDescription = $customerDescription;
                        }
                    }
                }
                $order->update();

                event(new SendOrderNotificationsEvent($order, $user, true));

                $this->addSubscriptions($order);
                break;
            default :
                $done = false;
                break;
        }
        return $done;
    }
}