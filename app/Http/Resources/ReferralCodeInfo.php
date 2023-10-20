<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ReferralCodeInfo extends JsonResource
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
            'code' => $this->when(isset($this->code), $this->code),
            'discount' => $this->when(isset($this->referralRequest->discount), $this->referralRequest->discount),
        ];
    }
}
