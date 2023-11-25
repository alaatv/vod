<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class BlockInChannelResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof \App\Models\Block)) {
            return [];
        }
        $contests = $this->notRedirectedContents;
        $sets = $this->sets;
        $products = $this->products;
        $banners = $this->banners;
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
            'contents' => optional($contests)->isNotEmpty() ? $contests : null,
            'sets' => optional($sets)->isNotEmpty() ? $sets : null,
            'products' => optional($products)->isNotEmpty() ? ProductInBlock::collection($products) : null,
            'banners' => optional($banners)->isNotEmpty() ? $banners : null,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'channels' => $this->resource->channels()?->without(['blocks'])->get(),
        ];
    }
}
