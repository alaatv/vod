<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\ShahrSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexShahrRequest;
use App\Models\Shahr;
use Illuminate\Http\JsonResponse;

class ShahrController extends Controller
{
    public function index(IndexShahrRequest $request, ShahrSearch $shahrSearch): JsonResponse
    {
        $filters = $request->all();
        $pageName = Shahr::INDEX_PAGE_NAME;

        $shahrSearch->setPageName($pageName);

        if ($request->has('length')) {
            $shahrSearch->setNumberOfItemInEachPage($request->get('length'));
        }

        $results = $shahrSearch->get($filters);

        return response()->json($results, 200);
    }
}