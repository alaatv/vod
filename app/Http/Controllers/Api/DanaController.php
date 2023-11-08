<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanaProductTransfer;
use App\Services\DanaProductService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class DanaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.TRANSFER_SET_TO_DANA'), ['only' => ['checkDanaToken',],]);
    }

    /**
     * @return JsonResponse
     */
    public function checkDanaToken(): JsonResponse
    {
        $testCourseId = DanaProductTransfer::first()->dana_course_id;
        $response = DanaProductService::getDanaSession($testCourseId);
        if ($response['status_code'] == ResponseAlias::HTTP_UNAUTHORIZED) {
            return response()->json(['error' => 'ارتباط با دانا به دلیل منقضی شدن توکن برقرار نیست'],
                ResponseAlias::HTTP_UNAUTHORIZED);
        }
        return response()->json(['success' => 'ارتباط با دانا برقرار است'], ResponseAlias::HTTP_OK);
    }
}