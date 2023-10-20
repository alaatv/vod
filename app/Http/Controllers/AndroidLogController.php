<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;

class AndroidLogController extends Controller
{

    public function failTrack(Request $request)
    {
        if (!Agent::isMobile()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        Log::channel('android')->info("Unknown app installation resource with ip: '{$request->ip()}' at: '".now()."'");

        return response('', Response::HTTP_OK);
    }
}
