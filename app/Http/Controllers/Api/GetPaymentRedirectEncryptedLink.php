<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Traits\OrderCommon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class GetPaymentRedirectEncryptedLink extends Controller
{
    use OrderCommon;

    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     *
     * @return array|JsonResponse
     */
    public function __invoke(Request $request)
    {
        $paymentMethod = $request->get('paymentMethod', 'saman');
        $device = $request->get('device', 'android');
        $orderId = $request->get('order_id');
        $transactionId = null;
        $orderId = !isset($orderId) ? $request->get('orderId') : $orderId;
        $inInstalment = $request->get('inInstalment');
        /** @var User $user */
        $user = $request->user();
        $seller = $request->input('seller', config('constants.ALAA_SELLER'));
        $device = $seller == 1 ? $device : 'web';
        $paymentMethod = $seller == 1 ? $paymentMethod : 'saman2';

        if ($request->has('transaction_id')) {
            $transactionId = $request->get('transaction_id');
        } else {
            if ($orderId) {
                $order = Order::findOrFail($orderId);
            } else {
                if ($inInstalment) {
                    $order = $user->getOpenOrderOrCreate($inInstalment, seller: $seller);
                } else {
                    $order = $user->getOpenOrderOrCreate(seller: $seller);
                }
            }
        }

        if (isset($order) && $order->orderproducts->isEmpty()) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'Your cart is empty');
        }

        $encryptedPostfix = $this->getEncryptedPostfix($user->id, $orderId, $seller, $inInstalment, $transactionId);

        $redirectTo = $this->getEncryptedUrl($paymentMethod, $device, $encryptedPostfix);
        if (!Str::contains($request->path(), 'v2')) {

            return response()->json([
                'url' => $redirectTo,
            ]);
        }

        $redirectTo = convertBaseUrlToAppUrl($redirectTo);
        return [
            'data' => [
                'url' => $redirectTo,
            ],
        ];
    }

    /**
     * @param  int  $userId
     * @param  int|null  $orderId
     *
     * @return string
     */
    private function getEncryptedPostfix(
        int $userId,
        ?int $orderId,
        ?int $seller,
        ?int $inInstalment,
        ?int $transactionId
    ): string {
        $toBeEncrypted = ['user_id' => $userId,];

        if (isset($orderId)) {
            $toBeEncrypted['order_id'] = $orderId;
        } else {
            if (isset($transactionId)) {
                $toBeEncrypted['transaction_id'] = $transactionId;
            }
        }
        if (isset($seller)) {
            $toBeEncrypted['seller'] = $seller;
        }
        if (isset($inInstalment)) {
            $toBeEncrypted['inInstalment'] = $inInstalment;
        }

        return encrypt($toBeEncrypted);
    }
}
