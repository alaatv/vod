<?php

namespace App\Http\Middleware;

use App\Traits\OrderproductTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiOrderCheckoutReview
{
    use OrderproductTrait;

    public function handle(Request $request, Closure $next)
    {
        if (!auth('api')->check()) {
            return $next($request);
        }
        $user = auth('api')->user();
        if ($request->has('order_id')) {
            return response([], Response::HTTP_FORBIDDEN);
        }
        $openOrder = $user->getOpenOrderOrCreate($request->input('has_instalment_option', 0),
            $request->input('seller', config('constants.ALAA_SELLER')));
        if ($openOrder->isInInstalment && $openOrder->orderproducts->count() > 1) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'بیش از یک محصول در سبد قسطی شما افزوده شده است.');
        }
        $request->offsetSet('order_id', $openOrder->id);
        if (!$request->has('cartItems')) {
            return $next($request);
        }

        $orderproducts = json_decode(json_encode($request->get('cartItems')));
        if ($this->validateOrderproducts($orderproducts)) {
            foreach ($orderproducts as $orderproduct) {
                $data = ['order_id' => $openOrder->id];
                $this->storeOrderproductJsonObject($orderproduct, $data);
            }
        }
        return $next($request);
    }

    /**
     * @param $cookieOrderproducts
     *
     * @return bool
     */
    private function validateOrderproducts($cookieOrderproducts): bool
    {
        return is_array($cookieOrderproducts) && !empty($cookieOrderproducts);
    }
}
