<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FAQ;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FaqPageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        return FAQ::collection(collect(alaaSetting()->faq));
    }
}
