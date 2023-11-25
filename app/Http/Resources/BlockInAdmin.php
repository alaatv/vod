<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Block
 *
 * @mixin \App\Models\Block
 *
 * @property mixed notRedirectedContents
 * @property mixed sets
 */
class BlockInAdmin extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Models\Block)) {
            return [

            ];
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
            'title' => $this->title,
            'offer' => $this->offer,
            'url' => $this->url,
            'order' => $this->order,
            'contents' => optional($contests)->isNotEmpty() ? $contests : null,
            'sets' => optional($sets)->isNotEmpty() ? $sets : null,
            'products' => optional($products)->isNotEmpty() ? $products : null,
            'banners' => optional($banners)->isNotEmpty() ? $banners : null,
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
        ];
    }
}
