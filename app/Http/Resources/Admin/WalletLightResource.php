<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Wallettype;
use App\Models\Wallet;
use Illuminate\Http\Request;

/**
 * Class WalletLightResource
 *
 * @mixin Wallet
 * */
class WalletLightResource extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Wallet)) {
            return [];
        }

        $this->loadMissing('walletType');

        return [
            'wallettype' => $this->when(isset($this->wallettype_id), function () {
                return new Wallettype($this->walletType);
            }),
            'balance' => $this->balance,
        ];
    }
}
