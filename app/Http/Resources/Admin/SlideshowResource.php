<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Block;
use App\Models\Slideshow;
use Illuminate\Http\Request;

class SlideshowResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof Slideshow)) {
            return [];
        }

        return [
            'id' => $this->id,
            'title' => $this->when(isset($this->title), $this->title),
            'blocks' => $this->when(isset($this->blocks), Block::collection($this->blocks)),
            'shortDescription' => $this->when(isset($this->shortDescription), $this->shortDescription),
            'link' => $this->when(isset($this->link), $this->link),
            'in_new_tab' => $this->when(isset($this->in_new_tab), $this->in_new_tab),
            'url' => $this->when(isset($this->photo), $this->url),
            'allowEdit' => auth()->user()->isAbleTo(config('constants.EDIT_SLIDESHOW_ACCESS')),
            'allowRemove' => auth()->user()->isAbleTo(config('constants.REMOVE_SLIDESHOW_ACCESS')),
        ];
    }
}
