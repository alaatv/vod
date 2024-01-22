<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Class ProductIndex
 *
 * @mixin \App\Models\Product
 * */
class ProductIndex extends AlaaJsonResource
{
    use Resource;

    public function __construct(\App\Models\Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
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
            'price' => $this->getPrice(),
            'url' => $this->getUrl(),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'redirect_code' => $this->when(isset($redirectUrl), Arr::get($redirectUrl, 'code')),
            'attributes' => new Attribute($this),
            'category' => $this->category,
            'variant' => '-',
            'is_purchased' => $this->is_purchased,
            'is_dependent' => $this->is_dependent,
            'enable' => $this->enable,
            'has_instalment_option' => $this->has_instalment_option,
            'payment_default' => $this->has_instalment_option ? 2 : 1,
            'instalments' => $this->instalments_detail,
        ];
    }
}
