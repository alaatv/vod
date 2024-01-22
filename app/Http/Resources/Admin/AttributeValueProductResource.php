<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Attributevalue;
use Illuminate\Http\Request;

/**
 * Class AttributeValueProductResource
 *
 * @mixin Attributevalue
 */
class AttributeValueProductResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Attributevalue)) {
            return [];
        }

        return [
            // TODO: These commented items are Attributevalue fields. I don't know how to use the AttributeValueResource in this resource.
            'attributevalue' => new AttributeValueResource(Attributevalue::find($this->id)),
            //            'id' => $this->id,
            //            'attribute' => new AttributeLightResource($this->attribute),
            //            'name' => $this->when(isset($this->name), $this->name),
            //            'values' => $this->when(isset($this->values), $this->values),
            //            'description' => $this->when(isset($this->description), $this->description),
            //            'is_default' => $this->when(isset($this->isDefault), $this->isDefault),
            //            'order' => $this->order,
            //            'created_at' => $this->when(isset($this->created_at), $this->created_at),
            //            'updated_at' => $this->when(isset($this->updated_at), $this->updated_at),

            'attributevalue_product' => $this->whenPivotLoaded('attributevalue_product', [
                'order' => $this->when(isset($this->pivot['order']), $this->pivot['order']),
                'extraCost' => $this->when(isset($this->pivot['extraCost']), $this->pivot['extraCost']),
                'description' => $this->when(isset($this->pivot['description']), $this->pivot['description']),
            ]),
        ];
    }
}
