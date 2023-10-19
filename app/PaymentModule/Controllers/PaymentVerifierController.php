<?php

namespace App\PaymentModule\Controllers;

use App\Events\SendOrderNotificationsEvent;
use App\Events\UserPurchaseCompleted;
use App\Models\Order;
use App\PaymentModule\Money;
use App\PaymentModule\Responses;
use App\Repositories\TransactionRepo;
use App\Traits\HandleOrderPayment;
use App\Traits\OrderCommon;
use App\Traits\User\AssetTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Payment;

class PaymentVerifierController extends Controller
{
    use HandleOrderPayment;
    use OrderCommon;
    use AssetTrait;

    /**
     * @param  string  $paymentMethod
     * @param  string  $device
     *
     * @return RedirectResponse
     */
    public function verify(\Illuminate\Http\Request $request, string $paymentMethod, string $device)
    {
        $authority = $request->get(config("payment.drivers.$paymentMethod.verification_token"));
        $responseMessages = [];

        config()->set('payment', config('payment'));
        config()->set('payment.default', $paymentMethod);

        $transaction = TransactionRepo::getTransactionByAuthority($authority)
            ->orFailWith([Responses::class, 'transactionNotFoundError']);

        $money = Money::fromTomans(abs($transaction->cost));

        // load the config file
        $paymentConfig = config('payment');//require(base_path() . '/config/payment.php');
        $payment = new Payment($paymentConfig);

        try {
            $receipt = $payment->amount($transaction->cost)->transactionId($transaction->authority)->verify();
            $responseMessages[] = 'پرداخت کاربر تایید شد.';
        } catch (InvalidPaymentException $exception) {
            $responseMessages[] = $exception->getMessage();
            $responseMessages['status'] = $exception->getCode();
        }

        /** @var Order $myOrder */
        $myOrder = $transaction->order;
        $myOrder->detachUnusedCoupon();
        $user = $myOrder->user;

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
            $referralCode = $myOrder->referralCode;
            if ($referralCode) {
                $referralCode->update([
                    'used_at' => $myOrder->completed_at,
                ]);
            }
            event(new SendOrderNotificationsEvent($myOrder, $user, true));

            $responseMessages = $this->makeResponseMessage($myOrder);

        } else {
            $this->handleOrderCanceledPayment($myOrder);
            $this->handleOrderCanceledTransaction($transaction);
            $transaction->gateway_status = Arr::get($responseMessages, 'status');
            $transaction->gateway_token = Request::input('_token');
            $transaction->update();
            $myOrdersPage = '
            <a href="'.action('Web\UserController@userOrders').'" class="btn btn-info m-btn--pill m-btn--air m-btn animated infinite heartBeat">
                سفارش های من
            </a>';
            $responseMessages[] =
                'یک سفارش پرداخت نشده به لیست سفارش های شما افزوده شده است که می توانید با رفتن به صفحه '.$myOrdersPage.' آن را پرداخت کنید';
        }

        setcookie('cartItems', '', time() - 3600, '/');

        /*
        if (isset($myOrder_id)) {} else { if (isset($transaction->wallet_id)) { if ($result['status']) { $this->handleWalletChargingSuccessPayment($gatewayVerify['RefID'], $transaction, $gatewayVerify['cardPanMask']); } else { $this->handleWalletChargingCanceledPayment($transaction); } } } */

        Request::session()
            ->flash('verifyResult', [
                'messages' => $responseMessages,
                'cardPanMask' => isset($receipt) ? $receipt->getDetail('CardHolderPan') : null,
                // we have no more this item
                'RefID' => isset($receipt) ? $receipt->getDetail('RefId') : '',
//                'isCanceled' => $verificationResult->isCanceled(), // we have no more this item
                'orderId' => $myOrder->id,
                'paidPrice' => $money->tomans(),
                'couponCode' => $code ?? null,
            ]);

        event(new UserPurchaseCompleted($myOrder));
        if ($myOrder->seller == config('constants.SOALAA_SELLER')) {
            return redirect('https://soalaa.com/order/'.$myOrder->id.'/thankYou');
        }

//        $ticket = Ticket::firstWhere(["user_id" => $user->id, "department_id" => 25]);
//        return redirect(config('constants.APP_URL') . '/panel/order/' . $myOrder->id . '/thankYou?ticket=' . $ticket ? $ticket->id : "0");
        return redirect(config('constants.APP_URL').'/panel/order/'.$myOrder->id.'/thankYou');
//        return redirect()->route('showOnlinePaymentStatus', [
//            'status'        => isset($receipt) ? 'successful' : 'failed',
//            'paymentMethod' => $paymentMethod,
//            'device'        => $device,
//        ]);
    }

    /**
     * @param  Order  $order
     *
     * @return array
     */
    private function handleOrderCanceledPayment(Order $order): void
    {
        if ($order->orderstatus_id == config('constants.ORDER_STATUS_OPEN')) {
            $order->close(config('constants.PAYMENT_STATUS_UNPAID'), config('constants.ORDER_STATUS_CANCELED'));
            $order->update();
        }
        //ToDo : Deprecated
        $order->refundWalletTransaction();
    }

    /**
     * @param $transaction
     */
    private function handleOrderCanceledTransaction($transaction): void
    {
        if ($transaction->transactionstatus_id != config('constants.TRANSACTION_STATUS_UNPAID')) //if it is not the payment for an instalment
        {
            $transaction->transactionstatus_id = config('constants.TRANSACTION_STATUS_UNSUCCESSFUL');
        }
    }
}
