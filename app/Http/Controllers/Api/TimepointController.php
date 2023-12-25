<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentTimePointWeb;
use App\Models\Timepoint;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class TimepointController extends Controller
{
    public function __construct()
    {
        $authException = $this->getAuthExceptionArray();
        $this->callMiddlewares($authException);
    }

    private function getAuthExceptionArray(): array
    {
        return [];
    }

    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
    }

    public function index()
    {
        return response()->json();
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->offsetSet('insertor_id', $request->user()->id);
        /** @var Timepoint $timepoint */
        $timepoint = Timepoint::query()->create($request->all());
        if (isset($timepoint)) {
            $content = $timepoint->content;
            Cache::tags(['content_'.$content->id.'_timepoints'])->flush();

            return ContentTimePointWeb::collection($content->times)->response();
        }

        return response()->json(['message' => 'Database error'], Response::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * Display the specified resource.
     *
     *
     * @return JsonResponse
     */
    public function show(Timepoint $timepoint)
    {
        return (new ContentTimePointWeb($timepoint))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     *
     * @return JsonResponse
     */
    public function update(Request $request, Timepoint $timepoint)
    {
        $updateResult = $timepoint->update($request->all());
        if ($updateResult) {
            $content = $timepoint->content;
            Cache::tags(['content_'.$content->id.'_timepoints'])->flush();

            return ContentTimePointWeb::collection($content->times)->response();
        }

        return response()->json(['message' => 'Database error'], Response::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Timepoint $timepoint)
    {
        $deleteResult = $timepoint->delete();
        if ($deleteResult) {
            $content = $timepoint->content;
            Cache::tags(['content_'.$content->id.'_timepoints'])->flush();

            return ContentTimePointWeb::collection($content->times)->response();
        }

        return response()->json(['message' => 'Database error'], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
