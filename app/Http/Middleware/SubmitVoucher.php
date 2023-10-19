<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class SubmitVoucher
{
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
        /** @var User $user */
        $user = Auth::guard($guard)->user();
        $code = $request->get('code');

        if (isset($user)) {
            return $next($request);
        }
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], ResponseAlias::HTTP_UNAUTHORIZED);
        }
        return redirect(route('web.voucher.submit.form', ['code' => $code]));
    }
}
