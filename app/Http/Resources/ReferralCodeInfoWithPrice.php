<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ReferralCodeInfoWithPrice extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $resource = $this->resource;
        return [
            'message' => $resource['message'],
            'referralCode' => new ReferralCodeInfo($resource['referralCode']),
            'price' => $this->when(isset($resource['priceInfo']),
                isset($resource['priceInfo']) ? new Price($resource['priceInfo']) : null),
        ];
    }
}
