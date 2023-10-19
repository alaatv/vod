<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SoalaaAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     *
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->ip() != config('constants.SOALAA_APP_IP')) {
            return response()->json(['data' => ['errorMsg' => 'invalid request ip']], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
