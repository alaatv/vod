<?php

namespace App\Http\Resources\Soalaa;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use JsonSerializable;

class SoalaaResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Product)) {
            return [];
        }
        $redirectUrl = $this->redirect_url;
        $grandsChildren = $this->grandsChildren()->active()->get();
        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'short_title' => $this->when(isset($this->shortName), $this->shortName),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'attributes' => new SoalaaAttributeResource($this),
            'category' => $this->category,
            'variant' => '-',
            'grandsChildren' => $this->when($grandsChildren?->isNotEmpty(),
                SoalaaChildResource::collection($grandsChildren)),
        ];
    }
}
