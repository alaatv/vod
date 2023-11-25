<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class Product
 *
 * @mixin \App\Models\Product
 * */
class ProductInWebAssetWithoutPagination extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Models\Product $model)
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
        if (!($this->resource instanceof \App\Models\Product)) {
            return [];
        }

        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'category' => $this->category,
            'price' => $this->getPrice(),
            'url' => $this->getUrl(),
            'photo' => $this->when(isset($this->photo), isset($this->photo) ? $this->photo : null),
            'sets' => $this->when($this->sets->isNotEmpty(), $this->sets->isNotEmpty() ? $this->getSet() : null),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
        ];
    }

    private function getSet()
    {
        return $this->sets->isNotEmpty() ? ProductSetForWeb::collection($this->whenLoaded('sets')) : null;
    }

}
