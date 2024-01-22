<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Wallet;
use Illuminate\Http\Request;

/**
 * Class WalletResource
 *
 * @mixin Wallet
 * */
class WalletResource extends AlaaJsonResource
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

        $this->loadMissing('walletType', 'user');

        return [
            'id' => $this->id,
            'user' => $this->when(isset($this->user_id), function () {
                return new UserLightResource($this->user);
            }),
            'wallettype' => $this->when(isset($this->wallettype_id), function () {
                return new WalletTypeLightResource($this->walletType);
            }),
            'balance' => $this->balance,
            'pending_to_reduce' => $this->pending_to_reduce,
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
        ];
    }
}
