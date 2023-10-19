<?php

namespace App\Http\Resources\Ewano;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class EwanoOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {

        if (!isset($this->resource?->result?->data?->id)) {
            return [];
        }

        return [
            'data' => [
                'ewano_order_id' => $this->resource->result->data->id,
                'alaa_order_id' => $this->resource->result->data->code,
                'amount' => $this->resource->result->data->totalAmount,
            ],
        ];
    }
}
