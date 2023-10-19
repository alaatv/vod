<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class userCanUseOneReferalCode
{

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = $request->user();
        if (optional($user)->hasUsedReferralCodes()) {
            return myAbort(ResponseAlias::HTTP_BAD_REQUEST, trans('yalda1400.use has used a code already'));
        }
        return $next($request);
    }
}
