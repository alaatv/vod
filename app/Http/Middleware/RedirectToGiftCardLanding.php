<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RedirectToGiftCardLanding
{

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $referralCodes = $request->user()->ownsReferralCode();
        if ($referralCodes->count() == 0) {
            return redirect()->route('web.landing.34');
        }
        return $next($request);
    }
}
