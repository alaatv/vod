<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Shahr;
use Illuminate\Http\Request;

class ShahrResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Shahr)) {
            return [];
        }

        $this->loadMissing('ostan');

        return [
            'id' => $this->id,
            'title' => $this->name,
            //            'shahr_type' => $this->when(isset($this->shahr_type), $this->shahr_type),
            //            'shahrestan' => $this->when(isset($this->shahrestan), $this->shahrestan),
            //            'bakhsh' => $this->when(isset($this->bakhsh), $this->bakhsh),
            'province' => $this->when(isset($this->ostan), function () {
                return new OstanResource($this->ostan);
            }),
            //            'amar_code' => $this->when(isset($this->amar_code), $this->amar_code),
        ];
    }
}
