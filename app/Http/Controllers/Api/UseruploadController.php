<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsertUserUploadRequest;
use App\Models\Consultationstatus;
use App\Models\Userupload;
use App\Models\Useruploadstatus;
use App\Notifications\CounselingStatusChanged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UseruploadController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->middleware('permission:'.config('constants.LIST_QUESTION_ACCESS'), ['only' => 'index']);
        $this->middleware('permission:'.config('constants.SHOW_QUESTION_ACCESS'), ['only' => 'show']);

        $this->response = new JsonResponse(null, 200);
    }

    public function index()
    {
        $questions = Userupload::all()->sortByDesc('created_at');
        $questionStatuses = Useruploadstatus::pluck('displayName', 'id');
        $counter = 1;

        return new JsonResponse(compact('questions', 'counter', 'questionStatuses'), 200);
    }

    public function store(InsertUserUploadRequest $request)
    {
        $userUpload = new Userupload();
        $userUpload->fill($request->all());
        $userUpload->user_id = Auth::user()->id;
        $userUpload_pending = Useruploadstatus::where('name', 'pending')->first();
        $userUpload->useruploadstatus_id = $userUpload_pending->id;

        if ($request->hasFile('consultingAudioQuestions')) {
            $file = $request->file('consultingAudioQuestions');
            $extension = $file->getClientOriginalExtension();
            $fileName = date('YmdHis').'.'.$extension;

            if (Storage::disk(config('disks.CONSULTING_AUDIO_QUESTIONS'))->put($fileName, File::get($file))) {
                $userUpload->file = $fileName;
            }

            if ($userUpload->save()) {
                return new JsonResponse(['message' => 'Successfully added counseling question'], 200);
            } else {
                return new JsonResponse(['message' => 'Database error'], 503);
            }
        } else {
            return new JsonResponse(['message' => 'No file sent'], 400);
        }
    }

    public function show(Userupload $userUpload)
    {
        $user = $userUpload->user;
        $counter = 0;
        $consultationStatuses = Consultationstatus::pluck('name', 'id');

        return new JsonResponse(compact('user', 'counter', 'consultationStatuses'), 200);
    }

    public function update(Request $request, Userupload $userupload)
    {
        $oldUserUploadStatus = $userupload->useruploadstatus_id;
        $userupload->fill($request->all());

        if (!$userupload->update()) {
            return new JsonResponse(['message' => 'Service Unavailable'], 503);
        }

        if ($oldUserUploadStatus == $userupload->useruploadstatus_id) {
            return new JsonResponse(null, 200);
        }

        $userUploadStatusName = Useruploadstatus::where('id',
            $userupload->useruploadstatus_id)->pluck('displayName')->toArray();
        $userupload->user->notify(new CounselingStatusChanged($userUploadStatusName[0]));

        return new JsonResponse(null, 200);
    }
}