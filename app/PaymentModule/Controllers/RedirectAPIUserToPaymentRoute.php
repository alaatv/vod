<?php

namespace App\PaymentModule\Controllers;

use App\Events\Authenticated;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RedirectAPIUserToPaymentRoute extends Controller
{
    public function __construct()
    {
//        $this->middleware('signed');
    }

    /**
     * redirect the user to online payment page
     *
     * @param  Request  $request
     * @param  string  $paymentMethod
     * @param  string  $device
     *
     * @return JsonResponse|RedirectResponse|Redirector
     */
    public function __invoke(string $paymentMethod, string $device, Request $request)
    {
        $decryptedData = (array) decrypt($request->get('encryptionData'));

        $userId = Arr::get($decryptedData, 'user_id');
        $orderId = Arr::get($decryptedData, 'order_id');
        $transactionId = Arr::get($decryptedData, 'transaction_id');
        $seller = Arr::get($decryptedData, 'seller');
        $inInstalment = Arr::get($decryptedData, 'inInstalment');

        $user = $this->getUser($userId)
            ->orFailWith([Response::class, 'sendErrorResponse', ['User not found', Response::HTTP_BAD_REQUEST]]);

        Auth::login($user);
        event(new Authenticated($user));

        $parameters = ['paymentMethod' => $paymentMethod, 'device' => $device];
        if (isset($orderId)) {
            $parameters = Arr::add($parameters, 'order_id', $orderId);
        }
        if (isset($transactionId)) {
            $parameters = Arr::add($parameters, 'transaction_id', $transactionId);
        }
        if (isset($seller)) {
            $parameters = Arr::add($parameters, 'seller', $seller);
        }
        if (isset($inInstalment)) {
            $parameters = Arr::add($parameters, 'inInstalment', $inInstalment);
        }
        return redirect(route('redirectToBank', $parameters));
    }

    private function getUser(int $userId)
    {
        $user = User::find($userId);

        return nullable($user, ['User not found', Response::HTTP_BAD_REQUEST]);
    }
}
