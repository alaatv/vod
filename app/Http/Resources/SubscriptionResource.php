<?php

namespace App\Http\Resources;

use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Product as ProductResource;
use App\Models\Event;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order' => new OrderResource($this->order),
            'subscription' => $this->when($this->relationLoaded('subscription'), function () {
                return match (true) {
                    $this->subscription instanceof \App\Models\Product => new ProductResource($this->subscription),
                    $this->subscription instanceof Event => new EventResource($this->subscription),
                    default => null
                };
            }),
            'valid_since' => $this->valid_since,
            'valid_until' => $this->valid_until,
            'values' => $this->values,
            'created_at' => $this->created_at,
        ];
    }
}
