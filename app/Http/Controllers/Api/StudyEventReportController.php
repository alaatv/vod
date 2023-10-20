<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudyEventReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StudyEventReportController extends Controller
{
    public function markAsRead(StudyEventReport $studyEventReport): JsonResponse
    {
        if (!Gate::allows('read-study-event-report', $studyEventReport)) {
            return myAbort(Response::HTTP_FORBIDDEN, 'permission denied');
        }
        $studyEventReport->update([
            'is_read' => StudyEventReport::READ_REPORT,
        ]);
        return response()->json([
            'message' => 'report marked as read',
        ]);
    }
}
