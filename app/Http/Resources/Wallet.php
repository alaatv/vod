<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Wallet
 *
 * @mixin \App\Wallet
 * */
class Wallet extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof \App\Wallet)) {
            return [];
        }

        $this->loadMissing('walletType');

        return [
            'id' => $this->id,
            'wallettype_id' => $this->when(isset($this->wallettype_id), function () {
                return new Wallettype($this->walletType);
            }),
            'balance' => $this->balance,
            'user' => $this->relationLoaded('user') ? $this->user : null
        ];
    }
}
