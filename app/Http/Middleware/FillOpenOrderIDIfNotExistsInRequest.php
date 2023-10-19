<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FillOpenOrderIDIfNotExistsInRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  null  $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user = Auth::guard($guard)->user();

        if (!isset($user)) {
            return $next($request);
        }

        if (!$request->has('order_id')) {
            /** @var User $user */
            $openOrder = $user->getOpenOrderOrCreate();
            $request->offsetSet('order_id', $openOrder->id);
            $request->offsetSet('openOrder', $openOrder);
        }

        return $next($request);
    }
}
