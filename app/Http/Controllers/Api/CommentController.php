<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CommentWithContentResource;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Class Comment.
 *
 * @package App\Http\Controllers\Api
 */
class CommentController extends Controller
{
    /**
     * Return a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Return the specified resource.
     */
    public function show(Comment $comment)
    {
        if (auth()->id() != $comment->author_id) {
            return myAbort(Response::HTTP_FORBIDDEN, 'عدم دسترسی!');
        }
        return new CommentWithContentResource($comment);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCommentRequest|Comment  $request
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request): JsonResponse
    {
        $model = config('constants.MORPH_MAP_MODELS')[$request->commentable_type]['model'];

        if (!class_exists($model)) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'کلاس نامعتبر!');
        }

        $comment = new Comment($request->all());

        $obj = $model::find($request->commentable_id);
        $obj->comments()->save($comment);

        return (new CommentResource($comment))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCommentRequest|Comment  $request
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        if (auth()->id() != $comment->author_id) {
            return myAbort(Response::HTTP_FORBIDDEN, 'عدم دسترسی!');
        }

        $comment->update(['comment' => $request->comment]);

        return (new CommentResource($comment))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Comment  $comment
     */
    public function destroy(Comment $comment)
    {
        if (auth()->id() != $comment->author_id) {
            return myAbort(Response::HTTP_FORBIDDEN, 'عدم دسترسی!');
        }
        $comment->delete();
        return \response()->json([
            'data' => [
                'id' => $comment->id,
                'message' => 'با موفقیت حدف شد.'
            ]
        ]);
    }
}
