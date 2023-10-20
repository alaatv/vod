<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ReferralCodeResource extends JsonResource
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
            'code' => $this->code,
            'enable' => $this->enable,
            'usageNumber' => $this->usageNumber,
            'isAssigned' => $this->isAssigned,
            'discount' => $this->referralRequest?->discount,
            'created_at' => $this->created_at,
            'url' => route('web.referralCode.show', $this->id),
            'used_at' => $this->used_at,
        ];
    }
}
