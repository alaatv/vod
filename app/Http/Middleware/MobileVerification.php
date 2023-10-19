<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MobileVerification
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
        /** @var User $user */
        $user = Auth::guard($guard)->user();

        if ((isset($user) && $user->hasVerifiedMobile())) {
            return $next($request);
        }
        if ($request->expectsJson()) {
            return myAbort(ResponseAlias::HTTP_UNAUTHORIZED, 'User is not verified');
        }

        return redirect(route('web.voucher.submit.form', ['code' => $request->get('code')]));
    }
}
