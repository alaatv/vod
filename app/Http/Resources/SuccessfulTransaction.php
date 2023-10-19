<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;


/**
 * Class Transaction
 *
 * @mixin Transaction
 * */
class SuccessfulTransaction extends AlaaJsonResource
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

        $this->loadMissing('paymentmethod', 'transactionstatus', 'transactiongateway');

        return [
            'wallet_id' => $this->when(isset($this->wallet_id), function () {
                return $this->wallet_id;
            }),
            'order_id' => $this->when(isset($this->order_id), function () {
                return $this->order_id;
            }),
            'cost' => $this->cost,
            'transactionID' => $this->when(isset($this->transactionID), $this->transactionID),
            'trace_number' => $this->when(isset($this->traceNumber), $this->traceNumber),
            'refrence_number' => $this->when(isset($this->referenceNumber), $this->referenceNumber),
            'paycheck_number' => $this->when(isset($this->paycheckNumber), $this->paycheckNumber),
            'paymentmethod' => $this->when(isset($this->paymentmethod_id), function () {
                return new Paymentmethod($this->paymentmethod);
            }),
            'transactiongateway' => $this->when(isset($this->transactiongateway_id), function () {
                return new Transactiongateway($this->transactiongateway);
            }),
            'transactionstatus' => $this->when(isset($this->transactionstatus_id), function () {
                return new TransactionStatus($this->transactionstatus);
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'completed_at' => $this->when(isset($this->completed_at), function () {
                return $this->completed_at;
            }),
            'deadline_at' => $this->when(isset($this->deadline_at), function () {
                return $this->deadline_at;
            }),
        ];
    }
}
