<?php

namespace App\Http\Middleware;

use App\Repositories\ProductvoucherRepo;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FindVoucher
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $code = $request->get('code');

        $voucher = ProductvoucherRepo::findVoucherByCode($code)->first();
        if (!is_null($voucher)) {

            $request->offsetSet('voucher', $voucher);

            return $next($request);
        }
        if ($request->expectsJson()) {
            return myAbort(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY, 'Voucher not found');
        }

        $flash = [
            'title' => 'خطا',
            'body' => 'کد شما یافت نشد',
        ];
        //TODO:// refactor
        setcookie('flashMessage', json_encode($flash), time() + (86400 * 30), '/');
        return redirect(route('web.voucher.submit.form', ['code' => $request->get('code')]));
    }
}
