<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class Product
 *
 * @mixin \App\Product
 * */
class Child extends AlaaJsonResource
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

        $this->loadMissing('children');
        $redirectUrl = $this->redirect_url;

        return [
            'id' => $this->id,
            'redirect_url' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'url')),
            'title' => $this->when(isset($this->name), $this->name),
            'price' => $this->getPrice(),
            'intro' => $this->when(isset($resource->intro_video),
                isset($this->intro_video) ? new IntroVideoOfProduct($this) : null),
            'url' => $this->getUrl(),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'attributes' => new Attribute($this),
            'children' => $this->when($this->children->isNotEmpty(), $this->getChildren()),
            'checked' => $this->when(isset($this->pivot->isDefault), function () {
                return isset($this->pivot->isDefault) ? (bool) $this->pivot->isDefault : false;
            }),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
            'is_default' => $this->pivot->isDefault,
        ];
    }
}
