<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWatchHistoryRequest;
use App\Http\Resources\WatchHistoryResource;
use App\Models\WatchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class WatchHistoryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreWatchHistoryRequest  $request
     * @return JsonResponse
     */
    public function store(StoreWatchHistoryRequest $request)
    {
        $model = config('constants.MORPH_MAP_MODELS')[$request->get('watchable_type')]['model'];

        if (!class_exists($model)) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'کلاس نامعتبر!');
        }

        if (!($obj = $model::find($request->get('watchable_id')))) {
            return response()->json(['message' => 'فیلد نامعتبر!'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $watchHistory = new WatchHistory($request->all());
        $obj->watches()->save($watchHistory);

        return (new WatchHistoryResource($watchHistory))->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function destroyByWatchableId(Request $request)
    {
        $model = config('constants.MORPH_MAP_MODELS')[$request->get('watchable_type')]['model'];

        if (!class_exists($model)) {
            return myAbort(Response::HTTP_BAD_REQUEST, 'Unresolvable model');
        }

        if (!($obj = $model::find($request->get('watchable_id')))) {
            return response()->json(['message' => 'Watchable not found'], Response::HTTP_BAD_REQUEST);
        }

        $watchHistory = WatchHistory::query()
            ->where('user_id', $request->user()->id)
            ->where('watchable_type', $model)
            ->where('watchable_id', $request->get('watchable_id'))
            ->first();

        if (!isset($watchHistory)) {
            return response()->json(['No history found to'], Response::HTTP_NOT_FOUND);
        }

        if ($watchHistory->delete()) {
            return response()->json(['message' => 'Removed']);
        }

        return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error');
    }

    public function bulkInsert(Request $request)
    {
        $userId = auth()->id();
        $watches = WatchHistory::where('user_id', $userId)->get();
        $watchables = [];
        foreach ($request->input('data') as $watchable) {
            if ($watches->contains('watchable_id', $watchable['watchable_id'])) {
                continue;
            }
            $watchable['user_id'] = $userId;
            $watchable['watchable_type'] = 'App\Models\Content';
            $watchable['created_at'] = now();
            $watchable['updated_at'] = now();
            $watchables[] = $watchable;
        }
        DB::table('watch_histories')->insert($watchables);
        return response()->json();
    }
}
