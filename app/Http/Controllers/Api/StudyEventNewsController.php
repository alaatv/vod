<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsertStudyEventNewsRequest;
use App\Models\StudyEventNews;
use Illuminate\Http\Response;

class StudyEventNewsController extends Controller
{
    public function __construct()
    {
        $this->callMiddleware();
    }

    private function callMiddleware()
    {
        $this->middleware('permission:'.config('constants.INSERT_LIVE_DESCRIPTION_ACCESS'), ['only' => ['store'],]);
    }

    public function store(InsertStudyEventNewsRequest $request)
    {
        $studyEventNews = StudyEventNews::create([
            'studyevent_id' => $request->get('studyevent_id'),
            'title' => $request->get('title'),
            'body' => $request->get('body'),
        ]);

        if (!isset($studyEventNews)) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'خطای پایگاه داده');
        }

        return response()->json();
    }
}
