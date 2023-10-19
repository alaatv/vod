<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Repositories\ProductRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class Yalda1400Middleware
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
        if (!ProductRepository::isYaldaProductActive()) {
            return myAbort(ResponseAlias::HTTP_BAD_REQUEST, trans('yalda1400.yalda product has been disabled'));
        }
        if (!$user->calcYaldaChances()) {
            return myAbort(ResponseAlias::HTTP_BAD_REQUEST, trans('yalda1400.no chances left for you'));
        }
        return $next($request);
    }
}
