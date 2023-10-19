<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class ApiLoginController extends LoginController
{
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->middleware('auth:api', ['only' => 'logout']);
        $this->middleware('convert:mobile|password|nationalCode');
    }

    public function logout(Request $request)
    {
        $request->user()
            ->token()
            ->revoke();

        return $this->loggedOut($request) ?: redirect('/');
    }

}
