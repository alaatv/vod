<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsertSourceRequest;
use App\Http\Resources\Source as SrouceResource;
use App\Models\Source;
use App\Traits\FileCommon;
use App\Traits\RequestCommon;
use Exception;
use Illuminate\Http\JsonResponse;

class SourceController extends Controller
{
    use FileCommon;
    use RequestCommon;

    public function __construct()
    {
        $this->callMiddlewares($this->getAuthExceptionArray());
    }

    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
        $this->middleware('role:'.config('constants.ROLE_ADMIN'));
    }

    private function getAuthExceptionArray(): array
    {
        return [];
    }

    public function index(): JsonResponse
    {
        $sources = Source::all();
        return response()->json(['sources' => $sources]);
    }

    public function store(InsertSourceRequest $request): JsonResponse
    {
        $file = $this->getRequestFile($request->all(), 'photo');

        $source = Source::create([
            'title' => $request->get('title'),
            'link' => $request->get('link'),
        ]);

        if ($file !== false) {
            $source->setPhoto($file, config('disks.SOURCE_PHOTO_MINIO'));
        }

        if ($source) {
            return response()->json(['message' => 'Source created successfully.'], 201);
        } else {
            return response()->json(['error' => 'Error creating source.'], 500);
        }
    }

    public function show(Source $source): JsonResponse
    {
        return response()->json(new SrouceResource($source));
    }

    public function edit(Source $source): JsonResponse
    {
        return response()->json(['source' => $source]);
    }

    public function update(InsertSourceRequest $request, Source $source): JsonResponse
    {
        $source->fill($request->all());

        $file = $this->getRequestFile($request->all(), 'photo');
        if ($file !== false) {
            $source->updatePhoto($file, config('disks.SOURCE_PHOTO_MINIO'));
        }

        if ($source->update()) {
            return response()->json(['message' => 'Source updated successfully.'], 200);
        } else {
            return response()->json(['error' => 'Error updating source.'], 500);
        }
    }

    public function destroy(Source $source): JsonResponse
    {
        try {
            $source->delete();
            return response()->json(['message' => 'Source has been removed successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => ['message' => 'Error on removing source']], 500);
        }
    }
}