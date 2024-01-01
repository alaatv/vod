<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\VastResource;
use App\Models\Contentset;
use Illuminate\Http\Request;

class SetResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        if (! ($this->resource instanceof Contentset)) {
            return [];
        }

        return [
            'id' => $this->id,
            'name' => $this->when(isset($this->name), $this->name),
            'small_name' => $this->when(isset($this->small_name), $this->small_name),
            'description' => $this->when(isset($this->description), $this->description),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'enable' => $this->enable,
            'display' => $this->display,
            'is_free' => $this->isFree,
            'edit_link' => action('Api\Admin\SetController@edit', $this->id),
            'contents_link' => route('api.set.list.contents', $this->id),
            'forrest_trees' => $this->forrest_tree,
            'forrest_tags' => $this->forrest_tags,
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'vast' => new VastResource($this->vast),
        ];
    }
}
