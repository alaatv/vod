<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeoRequest;
use App\Services\SeoService;

class SeoController extends Controller
{
    public function __invoke(SeoRequest $request, SeoService $seoService)
    {
        return response()->json([
            'data' => $seoService->setType($request->query('type'))->setModel($request->query('id'))->getSeo(),
        ]);
    }
}
