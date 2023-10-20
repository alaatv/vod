<?php

namespace App\Http\Middleware;

use App\Classes\Verification\MustVerifyMobileNumber;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;

class EnsureMobileIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return Response|RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        //TODO:// fix lang
        if (!$request->user() || ($request->user() instanceof MustVerifyMobileNumber && !$request->user()->hasVerifiedMobile())) {
            return $request->expectsJson() ? abort(Response::HTTP_FORBIDDEN,
                Lang::get('verification.Your mobile number is not verified.')) : Redirect::route('verification.notice');
        }

        return $next($request);
    }
}
