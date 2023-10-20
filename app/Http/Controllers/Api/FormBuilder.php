<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormBuilderRequest;
use App\Services\FormBuilderService;
use Illuminate\Http\JsonResponse;

class FormBuilder extends Controller
{
    public function __invoke(FormBuilderRequest $request, FormBuilderService $formBuilderService): JsonResponse
    {
        $resources = $formBuilderService->setTypes($request->query('types'))->getResources();

        return response()->json([
            'data' => $resources,
        ]);
    }
}
