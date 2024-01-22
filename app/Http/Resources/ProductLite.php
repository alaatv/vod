<?php

namespace App\Http\Resources;

use App\Traits\Product\Resource;
use Illuminate\Http\Request;

/**
 * Class Product
 *
 * @mixin \App\Models\Product
 */
class ProductLite extends AlaaJsonResource
{
    use Resource;

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

        return [
            'id' => $this->id,
            'title' => $this->when(isset($this->name), $this->name),
            'price' => $this->getPrice(),
            'photo' => $this->when(isset($this->photo), $this->photo),
            'url' => $this->getUrl(),
        ];
    }
}
