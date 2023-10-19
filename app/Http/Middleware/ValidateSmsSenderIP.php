<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ValidateSmsSenderIP
{
    private const VALIDA_IPS = ['87.107.115.30', '127.0.0.1'];

    public function handle(Request $request, Closure $next)
    {

        if (!in_array(request()->ip(), static::VALIDA_IPS)) {
            return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
