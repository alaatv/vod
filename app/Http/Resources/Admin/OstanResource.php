<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Ostan;
use Illuminate\Http\Request;

class OstanResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Ostan)) {
            return [];
        }

        return [
            'id' => $this->id,
            'title' => $this->name,
            //            'amar_code' => $this->when(isset($this->amar_code), $this->amar_code),
        ];
    }
}
