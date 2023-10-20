<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Wallettype;
use Illuminate\Http\Request;


/**
 * Class WalletTypeResource
 *
 * @mixin Wallettype
 * */
class WalletTypeResource extends AlaaJsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->when(isset($this->displayName), $this->displayName),
            'description' => $this->when(isset($this->description), $this->description),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
        ];
    }
}
