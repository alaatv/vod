<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class TransactionStatus
 *
 * @mixin \App\Transactionstatus
 * */
class TransactionStatus extends AlaaJsonResource
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
        return [
            'name' => $this->when(isset($this->displayName), $this->displayName),
            'id' => $this->when(isset($this->id), $this->id),
        ];
    }
}
