<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDraftContentRequest;
use App\Http\Requests\UpdateTempContentRequest;
use App\Models\Draft;
use App\Traits\DraftTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class DraftController extends Controller
{
    use DraftTrait;

    public function __construct()
    {
        $this->authorizeResource(Draft::class, 'draft');
    }

    public function index()
    {
        $drafts = Draft::all();
        return Response::json(['drafts' => $drafts], 200);
    }

    public function store(CreateDraftContentRequest $request)
    {
        $model = $this->setModel($request->model, $request->id);
        $draft = $model->drafts()->create([
            'draft_content' => $request->except('_token', 'model', 'id'),
            'author_id' => Auth::id(),
        ]);
        return Response::json(['message' => "پیشنهاد شما با موفقیت ثبت شد (شماره $draft->id)"], 201);
    }

    public function create(Request $request)
    {
        $model = $this->setModel($request->model, $request->id);
        return Response::json(['model' => $model], 200);
    }

    public function show(Draft $draft)
    {
        return Response::json(['draft' => $draft], 200);
    }

    public function edit(Draft $draft)
    {
        return Response::json(['draft' => $draft], 200);
    }

    public function update(UpdateTempContentRequest $request, Draft $draft)
    {
        DB::transaction(function () use ($draft, $request) {
            $draft->update([
                'draft_content' => $request->except(['accept', '_token', '_method']),
                'accepted_at' => $request->accept,
                'author_id' => Auth::id()
            ]);
            if ($draft->accepted_at) {
                $draft->draftable->update($request->validated());
                Draft::deactivateOthers($draft);
            }
        });
        return Response::json(['message' => 'پیشنهاد با موفقیت به روز رسانی شد'], 200);
    }

    public function destroy(Draft $draft)
    {
        if ($draft->accepted_at) {
            return Response::json(['message' => 'امکان حذف یک پیش نویس فعال وجود ندارد'], 403);
        }

        $message = "پیشنهاد $draft->id با موفقیت حذف شد";
        $draft->delete();
        return Response::json(['message' => $message, 'destroy' => true], 200);
    }
}