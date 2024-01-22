<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Transactionstatus;
use Illuminate\Http\Request;

/**
 * Class TransactionStatusLightResource
 *
 * @mixin Transactionstatus
 * */
class TransactionStatusLightResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Transactionstatus)) {
            return [];
        }

        return [
            'name' => $this->when(isset($this->name), $this->name),
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
        ];
    }
}
