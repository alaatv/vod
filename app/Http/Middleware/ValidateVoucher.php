<?php

namespace App\Http\Middleware;

use App\Models\Productvoucher;
use App\Models\Productvoucher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ValidateVoucher
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
        $code = $request->get('code');
        /** @var Productvoucher $voucher */
        $voucher = $request->get('voucher');
        $user = Auth::guard($guard)->user();


        if (isset($voucher) && !$voucher->isValid()) {
            if ($request->expectsJson()) {
                return myAbort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, 'Voucher is not valid');
            }

            $flash = [
                'title' => 'خطا',
                'body' => 'کد شما یافت نشد',
            ];
            setcookie('flashMessage', json_encode($flash), time() + (86400 * 30), '/');
            return redirect(route('web.voucher.submit.form', ['code' => $code ?? null]));
        }

        if (!(isset($voucher) && $voucher->hasBeenUsed())) {
            return $next($request);
        }
        if ($voucher->user_id == $user->id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Voucher has been used successfully',
                    'products' => $voucher->products,
                ]);
            }

            return redirect(route('web.user.asset'));
        }

        if ($request->expectsJson()) {
            return myAbort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, 'Voucher has been used before');
        }

        $flash = [
            'title' => 'خطا',
            'body' => 'کد قبلا استفاده شده است',
        ];
        setcookie('flashMessage', json_encode($flash), time() + (86400 * 30), '/');
        return redirect(route('web.voucher.submit.form', ['code' => $code ?? null]));
    }
}
