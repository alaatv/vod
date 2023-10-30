<?php

namespace App\Http\Controllers\Api;

use App\Events\UserPurchaseCompleted;
use App\Models\Order;
use App\PaymentModule\Money;
use App\PaymentModule\Responses;
use App\Repositories\TransactionRepo;
use App\Traits\HandleOrderPayment;
use App\Traits\OrderCommon;
use App\Traits\User\AssetTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Exceptions\InvoiceNotFoundException;
use Shetabit\Multipay\Payment;


class PaymentVerifierController extends Controller
{
    use HandleOrderPayment;
    use OrderCommon;
    use AssetTrait;

    /**
     * @param  Request  $request
     * @param  string  $paymentMethod
     * @param  string  $device
     * @return JsonResponse
     * @throws InvoiceNotFoundException
     * @throws Exception
     */
    public function verify(Request $request, string $paymentMethod, string $device): JsonResponse
    {
        $authority = $request->get(config("payment.drivers.$paymentMethod.verification_token"));
        $responseMessages = [];

        config()->set('payment', config('payment'));
        config()->set('payment.default', $paymentMethod);

        $transaction = TransactionRepo::getTransactionByAuthority($authority)
            ->orFailWith([Responses::class, 'transactionNotFoundError']);

        $money = Money::fromTomans(abs($transaction->cost));

        $paymentConfig = config('payment');
        $payment = new Payment($paymentConfig);

        try {
            $receipt = $payment->amount($transaction->cost)->transactionId($transaction->authority)->verify();
            $responseMessages[] = 'User payment has been verified.';
        } catch (InvalidPaymentException $exception) {
            $responseMessages[] = $exception->getMessage();
            $responseMessages['status'] = $exception->getCode();
        }

        $myOrder = $transaction->order;

        if (isset($receipt)) {
            $refId = match (true) {
                $paymentMethod == 'zarinpal' => Str::random(8),
                in_array($paymentMethod, ['saman', 'saman2']) => $request->input('TRACENO'),
                default => $receipt->getReferenceId()
            };
            TransactionRepo::handleTransactionStatus(
                $transaction,
                $refId,
                $receipt->getDetail('CardHolderPan') ?? '',
            );

            $this->handleOrderSuccessPayment($myOrder);
            event(new UserPurchaseCompleted($myOrder));

            $responseData = $this->makeResponseMessage($myOrder);

        } else {
            $this->handleOrderCanceledPayment($myOrder);
            $this->handleOrderCanceledTransaction($transaction);
            $transaction->gateway_status = Arr::get($responseMessages, 'status');
            $transaction->gateway_token = $request->input('_token');
            $transaction->update();

            $responseData = [
                'messages' => $responseMessages,
                'orderId' => $myOrder->id,
                'paidPrice' => $money->tomans(),
            ];
        }

        return response()->json($responseData);
    }

    private function handleOrderCanceledPayment(Order $order): void
    {
        if ($order->orderstatus_id == config('constants.ORDER_STATUS_OPEN')) {
            $order->close(config('constants.PAYMENT_STATUS_UNPAID'), config('constants.ORDER_STATUS_CANCELED'));
            $order->update();
        }
        //ToDo : Deprecated
        $order->refundWalletTransaction();
    }

    private function handleOrderCanceledTransaction($transaction): void
    {
        if ($transaction->transactionstatus_id != config('constants.TRANSACTION_STATUS_UNPAID')) {
            $transaction->transactionstatus_id = config('constants.TRANSACTION_STATUS_UNSUCCESSFUL');
        }
    }
}