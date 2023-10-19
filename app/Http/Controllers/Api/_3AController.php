<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserFor3A;
use Illuminate\Http\Request;

class _3AController extends Controller
{
    public function __construct()
    {
        $authException = $this->getAuthExceptionArray();
        $this->callMiddlewares($authException);
    }

    /**
     *
     * @return array
     */
    private function getAuthExceptionArray(): array
    {
        return [];
    }

    /**
     * @param $authException
     */
    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
    }

    /**
     * getUserFor3a
     *
     * @param  Request  $request
     *
     * @return UserFor3A
     */
    public function getUserFor3a(Request $request)
    {
        return new UserFor3A($request->user());
    }
}
