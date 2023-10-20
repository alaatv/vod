<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;


/**
 * Class Transaction
 *
 * @mixin Transaction
 * */
class UnpaidTransaction extends AlaaJsonResource
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request)
    {
        if (!($this->resource instanceof Transaction)) {
            return [];
        }
        $this->loadMissing('paymentmethod');


        return [
            'order_id' => $this->when(isset($this->order_id), function () {
                return $this->order_id;
            }),
            'cost' => $this->cost,
            'transactionstatus' => $this->when(isset($this->transactionstatus_id), function () {
                return new TransactionStatus($this->transactionstatus);
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'deadline_at' => $this->when(isset($this->deadline_at), $this->deadline_at),
            'id' => $this->id,
        ];
    }
}
