<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

class BlockWithBlockableResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->when(isset($this->type), function () {
                return new BlockType($this->type);
            }),
            'block_type' => $this->when(isset($this->type), function () {
                return new BlockTypeResource($this->blockType);
            }),
            'title' => $this->title,
            'offer' => $this->offer,
            'url' => $this->url,
            'order' => $this->order,
            'blockable_info' => BlockableResource::collection($this->blockables),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'edit_link' => route('block.edit', $this->id),
            'channels' => $this->resource->channels()?->without(['blocks'])->get(),
        ];
    }
}
