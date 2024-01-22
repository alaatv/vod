<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\ProductLite;
use App\Models\Orderproduct;
use Illuminate\Http\Request;

/**
 * Class Orderproduct
 *
 * @mixin Orderproduct
 * */
class LightPurchasedOrderproduct extends AlaaJsonResource
{
    public function __construct(Orderproduct $model)
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
        if (!($this->resource instanceof Orderproduct)) {
            return [];
        }

        $this->loadMissing('product', 'product.grand', 'product.grand', 'orderproducttype', 'attributevalues');

        return [
            'id' => $this->id,
            'product' => $this->when(isset($this->product_id), $this->getProduct()),
        ];
    }

    private function getProduct()
    {
        return isset($this->product_id) ? new ProductLite($this->product) : null;
    }
}
