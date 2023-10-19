<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CanAccessEmployeeTimeSheet
{
    public const EXCLUDED_USERS = [1371080, 1045878, 775163];

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
        /** @var User $user */
        $user = $request->user();

//      if (!$user->hasRole(config('constants.ROLE_ADMIN')) && !in_array($request->ip(), config('constants.ALAA_IP')) && !in_array($user->id, self::EXCLUDED_USERS))
//      if ( !in_array($request->ip(), config('constants.ALAA_IP')) && in_array($user->id, self::EXCLUDED_USERS))
//      {
//          return response()->json(['error' => 'This request is forbidden from you IP',], ResponseAlias::HTTP_FORBIDDEN);
//      }

        return $next($request);
    }
}
