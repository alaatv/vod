<?php

namespace App\Http\Controllers\Api;

use App\Models\Conductor;
use App\Models\Live;
use App\Repositories\ConductorRepo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LiveController
{
    public function startLive(Request $request)
    {
        $today = Carbon::today()->setTimezone('Asia/Tehran');
        $now = Carbon::now('Asia/Tehran');
        $nowTime = $now->toTimeString();
        $todayStringDate = $today->toDateString();

        $liveStream = ConductorRepo::isThereLiveStream($todayStringDate)->first();
        if (isset($liveStream)) {
            return response()->json([
                'A live is already going on right now',
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->insertLiveConductor($nowTime, $todayStringDate, $request->get('title'));
        if ($result) {
            Cache::tags('live')->flush();
            return response()->json([
                'live started successfully',
            ]);
        }

        return response()->json([
            'DB error on inserting into conductor',
        ], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    private function insertLiveConductor(
        string $startTime,
        string $todayStringDate,
        string $title = null,
        Live $scheduledLive = null
    ): Conductor {
        return Conductor::create([
            'title' => (strlen($title) > 0) ? $title : optional($scheduledLive)->title,
            'description' => optional($scheduledLive)->description,
            'poster' => optional($scheduledLive)->poster,
            'date' => $todayStringDate,
            'scheduled_start_time' => optional($scheduledLive)->start_time,
            'scheduled_finish_time' => optional($scheduledLive)->finish_time,
            'start_time' => $startTime,
        ]);
    }

    public function endLive(Request $request)
    {
        $today = Carbon::today()->setTimezone('Asia/Tehran');
        $now = Carbon::now('Asia/Tehran');
        $nowTime = $now->toTimeString();
        $todayStringDate = $today->toDateString();


        $liveStream = ConductorRepo::isThereLiveStream($todayStringDate)->first();
        if (!isset($liveStream)) {

            return response()->json([
                'live not found',
            ], ResponseAlias::HTTP_NOT_FOUND);
        }
        Cache::tags('live')->flush();
        $liveStream->update([
            'finish_time' => $nowTime,
        ]);

        return response()->json([
            'live ended successfully',
        ]);
    }
}