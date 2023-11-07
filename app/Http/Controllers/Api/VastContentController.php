<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyVastContentRequest;
use App\Http\Requests\StoreVastContentRequest;
use App\Models\Vast;
use Illuminate\Support\Facades\Cache;

class VastContentController extends Controller
{
    public function index(Vast $vast)
    {
        return response()->json([
            'vast' => $vast,
            'contents' => $vast->contents,
        ], 200);
    }

    public function store(StoreVastContentRequest $request, Vast $vast)
    {
        $ids = $request->input('ids');

        $this->flushVastContentCache($ids);
        $vast->contents()->attach($ids, $request->only(['valid_since', 'valid_until']));

        return response()->json(['message' => 'Content added to Vast successfully'], 200);
    }

    public function flushVastContentCache(array $ids)
    {
        foreach ($ids as $id) {
            Cache::tags(["content_{$id}_vast"])->flush();
        }
    }

    public function destroy(DestroyVastContentRequest $request, Vast $vast, string $content)
    {
        $ids = $request->input('ids', [$content]);

        $this->flushVastContentCache($ids);
        $vast->contents()->detach($ids);

        return response()->json(['message' => 'Content removed from Vast successfully'], 200);
    }
}