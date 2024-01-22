<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\ContentStatusResource;
use App\Models\Content;
use Illuminate\Http\Request;

class ContentResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof Content)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
            'enable' => $this->enable,
            'content_type' => $this->when(isset($this->contenttype_id), function () {
                return new ContentTypeResource($this->contenttype);
            }),
            'description' => $this->when(isset($this->description), $this->description),
            'display' => $this->display,
            'is_free' => $this->isFree,
            'edit_link' => action('Web\ContentController@edit', $this->id),
            'remove_link' => action('Web\ContentController@destroy', $this->id),
            'valid_since' => $this->when(isset($this->validSince), function () {
                return optional($this->validSince)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'is_favored' => $this->is_favored,
            'status' => $this->when(isset($this->content_status_id), function () {
                return new ContentStatusResource($this->status);
            }),
            'photo' => $this->when(isset($this->thumbnail), $this->thumbnail),
        ];
    }
}
