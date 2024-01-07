<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class OverwriteOrderIDAndAddItToRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        /** @var User $user */
        $user = $request->user();
        $isInInstalmentOrder = $request->get('has_instalment_option', 0);

        if ($orderId = $request->get('order_id')) {
            $openOrder = Order::findOrFail($orderId);
            if ($openOrder->user_id != $user->id || $openOrder->paymentstatus_id != config('constants.PAYMENT_STATUS_UNPAID')) {
                abort(403, 'Access denied');
            }
        } else {
            $seller = $request->get('seller', config('constants.ALAA_SELLER'));
            $openOrder = $user->getOpenOrderOrCreate($isInInstalmentOrder, $seller);
        }

        //ToDo : This is a temporary restriction which will be eliminated when in instalment cart will have been ready
        if ($isInInstalmentOrder && $openOrder->orderproducts->count() > 0) {
            $openOrder->orderproducts()->delete();
        }

        $request->offsetSet('order_id', $openOrder->id);
        $request->offsetSet('openOrder', $openOrder);

        return $next($request);
    }
}
