<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Traits\MorphTrait;
use Illuminate\Http\Request;


/**
 * Class Block
 *
 * @mixin Comment
 */
class CommentResource extends AlaaJsonResource
{
    use MorphTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  Request|Comment  $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof Comment)) {
            return [];
        }

        return [
            'id' => $this->id,
            'commentable_id' => $this->commentable_id,
            'commentable_type' => $this->commentable_type,
            'commentable' => $this->when($this->relationLoaded('resource'), function () {
                return $this->getResourceByModel('commentable');
            }),
            'comment' => $this->comment,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
        ];
    }
}
