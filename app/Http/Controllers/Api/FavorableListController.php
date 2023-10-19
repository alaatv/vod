<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FavorableListRequest;
use App\Http\Resources\FavorableListResource;
use App\Models\FavorableList;
use App\Models\FavorableList;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FavorableListController extends Controller
{
    public function index()
    {
        $favorableLists = auth()->user()->favorableLists()->orderBy('order', 'asc')->get();
        return FavorableListResource::collection($favorableLists);
    }

    public function store(FavorableListRequest $request)
    {
        $favorableList = auth()->user()->favorableLists()->create($request->validated());
        return new FavorableListResource($favorableList);
    }

    public function show(FavorableList $favorableList)
    {
        if (!Gate::allows('show-update-delete-favorable-list', $favorableList)) {
            return myAbort(Response::HTTP_FORBIDDEN, 'دسترسی ندارید');
        }
        return new FavorableListResource($favorableList);
    }

    public function update(FavorableListRequest $request, FavorableList $favorableList)
    {
        if (!Gate::allows('show-update-delete-favorable-list', $favorableList)) {
            return myAbort(Response::HTTP_FORBIDDEN, 'دسترسی ندارید');
        }
        $favorableList->update($request->validated());
        return new FavorableListResource($favorableList);
    }

    public function destroy(FavorableList $favorableList)
    {
        if (!Gate::allows('show-update-delete-favorable-list', $favorableList)) {
            return myAbort(Response::HTTP_FORBIDDEN, 'دسترسی ندارید');
        }
        $favorableList->delete();
        return new FavorableListResource($favorableList);
    }
}
