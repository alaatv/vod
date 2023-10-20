<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Wallettype;
use Illuminate\Http\Request;


/**
 * Class WalletTypeLightResource
 *
 * @mixin Wallettype
 * */
class WalletTypeLightResource extends AlaaJsonResource
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
        if (!($this->resource instanceof Wallettype)) {
            return [];
        }

        return [
            'name' => $this->name,
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
        ];
    }
}
