<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Block
 *
 * @mixin \App\Block
 * */
class Channel extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Channel)) {
            return [];
        }

        $sets = $this->blocks->first()?->sets;

        return [
            'id' => $this->id,
            'title' => $this->when(isset($this->title), $this->title),
            'description' => $this->when(isset($this->description), $this->description),
            'photo' => $this->when(isset($this->thumbnail), $this->thumbnail),
            'url' => $this->getUrl($this),
            'sets' => isset($sets) ? SetInIndexWithoutPagination::collection($sets) : null,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'future_blocks' => BlockInChannelResource::collection($this->blocks()->where('enable', 0)->get()),
            'normal_blocks' => BlockInChannelResource::collection($this->blocks()->enable()->get()),
        ];
    }

    private function getUrl($channel)
    {
        return new Url($channel);
    }
}
