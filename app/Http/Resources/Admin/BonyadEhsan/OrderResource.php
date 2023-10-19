<?php

namespace App\Http\Resources\Admin\BonyadEhsan;

use App\Http\Resources\Admin\LightPurchasedOrderproduct;
use App\Http\Resources\Admin\UserLightResource;
use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\User;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Class OrderResource
 *
 * @mixin Order
 * */
class OrderResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Order)) {
            return [];
        }

        $this->loadMissing('orderproducts');

        return [
            'id' => $this->id,
            'orderproducts' => $this->when(isset($this->orderproducts), function () {
                return LightPurchasedOrderproduct::collection($this->whenLoaded('orderproducts'));
            }),
            'user' => $this->when(isset($this->user_id), function () {
                return new User($this->user);
            }),
            'insertor' => new UserLightResource($this->activities->first()?->causer),
            'completed_at' => $this->when(isset($this->completed_at), $this->completed_at),
        ];
    }
}
