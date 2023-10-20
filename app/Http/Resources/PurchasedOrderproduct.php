<?php

namespace App\Http\Resources;

use App\Models\Orderproduct;
use Illuminate\Http\Request;


/**
 * Class Orderproduct
 *
 * @mixin Orderproduct
 * */
class PurchasedOrderproduct extends AlaaJsonResource
{
    public function __construct(Orderproduct $model)
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
        if (!($this->resource instanceof Orderproduct)) {
            return [];
        }

        $this->loadMissing('product', 'product.grand', 'product.grand', 'orderproducttype', 'attributevalues');

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'type' => $this->when(isset($this->orderproducttype_id), $this->orderproducttype_id),
            'product' => $this->when(isset($this->product_id), $this->getProduct()),
            'grand' => $this->when($this->haseGrand(), $this->getGrand()),
            'price' => $this->price,
            'photo' => $this->when(isset($this->photo), $this->photo),
            'extra_attributes' => $this->when($this->hasAttributesValues(), $this->getAttributeValues()),
            //Not a relationship
            'expire_at' => $this->expire_at,
        ];
    }

    private function getProduct()
    {
        return isset($this->product_id) ? new PurchasedProduct($this->product) : null;
    }

    /**
     * @return bool
     */
    private function haseGrand(): bool
    {
        return isset($this->product_id) && isset($this->product->grand_id);
    }

    private function getGrand()
    {
        return $this->haseGrand() ? new GrandProduct($this->product->grand) : null;
    }

    /**
     * @return bool
     */
    private function hasAttributesValues(): bool
    {
        return $this->attributevalues->isNotEmpty();
    }

    private function getAttributeValues()
    {
        return $this->hasAttributesValues() ? Attributevalue::collection($this->attributevalues) : null;
    }
}
