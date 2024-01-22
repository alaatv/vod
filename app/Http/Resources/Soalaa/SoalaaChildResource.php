<?php

namespace App\Http\Resources\Soalaa;

use App\Http\Resources\AlaaJsonResource;
use App\Traits\Product\Resource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use JsonSerializable;

class SoalaaChildResource extends AlaaJsonResource
{
    use Resource;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {

        $redirectUrl = $this->redirect_url;
        $children = $this->grandsChildren()->active()->orderBy('created_at')->get();

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'short_title' => $this->when(isset($this->shortName), $this->shortName),
            'price' => $this->getPrice(),
            'attributes' => new SoalaaChildAttributeResource($this),
            'children' => $this->when($children, SoalaaChildResource::collection($children)),
        ];
    }
}
