<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Order
 *
 * @mixin \App\Transaction
 * */
class Transaction extends AlaaJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        if (!($this->resource instanceof \App\Transaction)) {
            return [];
        }

        $this->loadMissing('paymentmethod', 'transactionstatus');

        return [
            'id' => $this->id,
            'payment_method' => $this->when(isset($this->paymentmethod_id), function () {
                return new Paymentmethod($this->paymentmethod);
            }),
            'transaction_status' => $this->when(isset($this->transactionstatus_id), function () {
                return new TransactionStatus($this->transactionstatus);
            }),
            'order_id' => $this->when(isset($this->order_id), $this->order_id),
            'transaction_id' => $this->when(isset($this->transactionID), $this->transactionID),
            'trace_number' => $this->when(isset($this->traceNumber), $this->traceNumber),
            'reference_number' => $this->when(isset($this->referenceNumber), $this->referenceNumber),
            'paycheck_number' => $this->when(isset($this->paycheckNumber), $this->paycheckNumber),
            'cost' => $this->when(isset($this->cost), $this->cost),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
            'completed_at' => $this->when(isset($this->completed_at), function () {
                return $this->completed_at ?? null;
            }),
            'user' => $this->when(isset($this->destinationBankAccount_id), function () {
                return $this->destinationBankAccount->user?->full_name ?? '--';
            }),
            'destination_shaba_number' => $this->when(isset($this->destinationBankAccount_id), function () {
                return $this->destinationBankAccount->shabaNumber ?? '--';
            }),
        ];
    }
}
