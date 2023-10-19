<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class OrderTransactionCommissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $commissionPercentage = $this->order?->referralCode?->referralRequest?->default_commission;
        $op_price = $this->op_share_amount;
        $commission = ($commissionPercentage / 100) * $op_price;
        return [
            'id' => $this->op_id,
            'full_name' => $this->order?->user?->full_name ?? '--',
            'code' => $this->order?->referralCode?->code,
            'product' => $this->product?->name,
            'product_price' => $op_price,
            'purchased_at' => $this->t_completed_at,
            'commisson' => $commission,
            'commisson_percentage' => $commissionPercentage,
        ];
    }
}
