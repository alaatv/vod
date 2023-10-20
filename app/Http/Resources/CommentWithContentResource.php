<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class CommentWithContentResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Comment)) {
            return [];
        }

        $commentable = $this->commentable;
        return [
            'id' => $this->id,
            'commentable_id' => $this->commentable_id,
            'commentable_type' => $this->commentable_type,
            'comment' => $this->comment,
            'content' => (object) ['id' => $commentable->id, 'title' => $commentable->name],
            'set' => (object) ['id' => $commentable->contentset_id, 'short_title' => $commentable->set->small_name],
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
        ];
    }
}
