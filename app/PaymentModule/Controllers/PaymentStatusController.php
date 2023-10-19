<?php

namespace App\PaymentModule\Controllers;

use App\Models\User;
use App\PaymentModule\GtmEec;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class PaymentStatusController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     *
     * @param  string  $status
     * @param  string  $paymentMethod
     * @param  string  $device
     *
     * @return Factory|RedirectResponse|View
     */
    public function show(\Illuminate\Http\Request $request, string $status, string $paymentMethod, string $device)
    {
        $sessionToken = Session::token();
        $result = Request::session()->pull('verifyResult');

        /** @var User $user */
        $user = $request->user();
        $needCompleteInfo = (isset($user) && $user->completion() < 60) ? true : false;
        if ($result == null) {
            return redirect()->action('Web\UserController@userOrders');
        }

        $gtmEec = (new GtmEec())->generateGtmEec($result['orderId'], $device, $result['paidPrice']);

        return view('order.checkout.verification', compact('status', 'paymentMethod',
            'device', 'result', 'gtmEec', 'user', 'needCompleteInfo', 'sessionToken'));
    }
}
