<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\Models\User;
use App\Traits\OrderproductTrait;
use Closure;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class OrderCheckoutReview
{
    use OrderproductTrait;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @param  null  $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check()) {
            return $next($request);
        }
        /** @var User $user */
        $user = Auth::guard($guard)->user();


        if ($orderId = $request->get('order_id')) {
            $openOrder = Order::findOrFail($orderId);
            if ($openOrder->user_id != $user->id || $openOrder->paymentstatus_id != config('constants.PAYMENT_STATUS_UNPAID')) {
                abort(403, 'Access denied');
            }
        } else {
            $openOrder = $user->getOpenOrderOrCreate($request->get('has_instalment_option', 0));
        }

        //ToDo : This is a temporary restriction which will be eliminated when in instalment cart will have been ready
        if ($openOrder->isInInstalment && $openOrder->orderproducts->count() > 1) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'بیش از یک محصول در سبد قسطی شما افزوده شده است.');
        }
        $request->offsetSet('order_id', $openOrder->id);

        if (!$request->hasCookie('cartItems')) {
            setcookie('cartItems', 'Expired', time() - 100000, '/');
            Cookie::queue(cookie()->forget('cartItems'));
            return $next($request);
        }

        $cookieOrderproducts = json_decode($request->cookie('cartItems'), false, 512, JSON_THROW_ON_ERROR);
        if ($this->validateCookieOrderproducts($cookieOrderproducts)) {
            foreach ($cookieOrderproducts as $cookieOrderproduct) {
                $data = ['order_id' => $openOrder->id];
                $this->storeOrderproductJsonObject($cookieOrderproduct, $data);
            }
        }

        setcookie('cartItems', 'Expired', time() - 100000, '/');
        Cookie::queue(cookie()->forget('cartItems'));

        return $next($request);
    }

    /**
     * @param $cookieOrderproducts
     *
     * @return bool
     */
    private function validateCookieOrderproducts($cookieOrderproducts): bool
    {
        return is_array($cookieOrderproducts) && !empty($cookieOrderproducts);
    }
}
