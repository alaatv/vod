<?php

namespace App\Http\Controllers\Api;

use App\Events\GetLiveConductor;
use App\Http\Controllers\Controller;
use App\Http\Resources\LiveConductorResource;
use App\Models\Conductor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LiveConductorController extends Controller
{
    public function view(Request $request)
    {
        $liveConductor = Conductor::where('class_name', $request->input('class_name'))
            ->whereDate('date', Carbon::today()->setTimezone('Asia/Tehran'))
            ->where('start_time', '<', now()->setTimezone('Asia/Tehran'))
            ->where('finish_time', '>', now()->setTimezone('Asia/Tehran'))
            ->first();
        if (!isset($liveConductor)) {
            return myAbort(Response::HTTP_FAILED_DEPENDENCY, 'زنگی یافت نشد');
        }
        GetLiveConductor::dispatch($liveConductor, auth()->id());
        return response()->json([
            'message' => "live conductor's user has been updated successfully",
        ]);
    }

    public function show(Conductor $liveConductor)
    {
        if (!($liveConductor->date == Carbon::today()->toDateString()
            && $liveConductor->start_time < now()->setTimezone('Asia/Tehran')
            && $liveConductor->finish_time > now()->setTimezone('Asia/Tehran'))) {
            return myAbort(Response::HTTP_FAILED_DEPENDENCY, 'لایو هنوز شرووع نشده است');
        }
        GetLiveConductor::dispatch($liveConductor, auth()->id());
        return new LiveConductorResource($liveConductor);
    }
}
