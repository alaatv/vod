<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;


/**
 * Class User
 *
 * @mixin Comment
 * */
class CommentLightResource extends AlaaJsonResource
{
    public function __construct(Comment $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Comment)) {
            return [];
        }

        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
        ];
    }
}
