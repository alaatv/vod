<?php

namespace App\Http\Resources;

use App\Models\Productvoucher;
use Illuminate\Http\Request;

/**
 * Class \App\Models\Productvoucher
 *
 * @mixin Productvoucher
 */
class HekmatVoucher extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Productvoucher)) {
            return [];
        }

        return [
            'enable' => $this->isEnable(),
            'is_expired' => $this->isExpired(),
            'used_at' => $this->used_at,
            'user' => $this->when(!is_null($this->user_id), function () {
                if (isset($this->user_id)) {
                    return new HekmatVoucherUser($this->user);
                }

                return null;
            }),
            'products' => $this->when(!is_null($this->getRawOriginal('products')), function () {
                if (isset($this->products)) {
                    return HekmatVoucherProduct::collection($this->products);
                }

                return null;
            }),
        ];
    }
}
