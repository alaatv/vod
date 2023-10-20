<?php

namespace App\PaymentModule;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

class Responses
{
    public static function noResponseFromBankError()
    {
        return self::sendErrorResponse('No response from bank', Response::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * @param  string  $msg
     * @param  int  $statusCode
     *
     * @return JsonResponse
     */
    private static function sendErrorResponse(string $msg, int $statusCode)
    {
        return response()->json(['message' => $msg], $statusCode);
    }

    public static function editTransactionError()
    {
        return self::sendErrorResponse('مشکلی در ویرایش تراکنش رخ داده است.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function gateWayNotFoundError()
    {
        return self::sendErrorResponse('درگاه مورد نظر یافت نشد', Response::HTTP_BAD_REQUEST);
    }

    public static function transactionNotFoundError()
    {
        return self::sendErrorResponse('تراکنشی متناظر با شماره تراکنش ارسالی یافت نشد.', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param  string  $device
     * @param  Order  $order
     * @param  string  $customerDescription
     *
     * @return RedirectResponse|Redirector
     */
    public static function sendToOfflinePaymentProcess(string $device, Order $order, string $customerDescription = null)
    {
        //It is not best practice to send $customerDescription in request body because it is a GET request
        // I have to either update order before going to verifyOfflinePayment (which costs an extra query)
        // or put $customerDescription in Session
        session()->flash('customerDescription', $customerDescription);

        return redirect()->route('verifyOfflinePayment', [
            'device' => $device,
            'paymentMethod' => 'wallet',
            'coi' => isset($order) ? $order->id : null,
        ]);

    }
}
