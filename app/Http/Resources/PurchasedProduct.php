<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class PurchasedProduct
 *
 * @mixin \App\Product
 * */
class PurchasedProduct extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Product $model)
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
        if (!($this->resource instanceof \App\Product)) {
            return [];
        }

        $this->loadMissing('sets', 'children', 'producttype');
        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'type' => $this->when(isset($this->producttype_id), $this->getType()),
            'category' => $this->when(isset($this->category), $this->category),
            'title' => $this->when(isset($this->name), $this->name),
            'is_free' => $this->isFree,
            'url' => $this->getUrl(),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'attributes' => new Attribute($this),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
        ];
    }
}
