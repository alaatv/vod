<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Models\Transaction;
use Illuminate\Http\Request;


/**
 * Class TransactionResource
 *
 * @mixin Transaction
 */
class TransactionResource extends AlaaJsonResource
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
        if (!($this->resource instanceof Transaction)) {
            return [];
        }

        $this->loadMissing(
            'wallet',
            'order',
            'sourceBankAccount',
            'destinationBankAccount',
            'paymentmethod',
            'device',
            'transactiongateway',
            'transactionstatus'
        );

        return [
            'id' => $this->id,
            'order_id' => $this->when(isset($this->order_id), $this->order_id),
            'wallet' => $this->when(isset($this->wallet_id), function () {
                return new WalletLightResource($this->wallet);
            }),
            'cost' => $this->when(isset($this->cost), $this->cost),
            'authority' => $this->when(isset($this->authority), $this->authority),
            'transaction_id' => $this->when(isset($this->transactionID), $this->transactionID),
            'trace_number' => $this->when(isset($this->traceNumber), $this->traceNumber),
            'reference_number' => $this->when(isset($this->referenceNumber), $this->referenceNumber),
            'paycheck_number' => $this->when(isset($this->paycheckNumber), $this->paycheckNumber),
            'manager_comment' => $this->when(isset($this->managerComment), $this->managerComment),
            'source_bank_account_id' => $this->when(isset($this->source_bank_account_id), $this->sourceBankAccount_id),
            'destination_bank_account_id' => $this->when(isset($this->destination_bank_account_id),
                $this->destinationBankAccount_id),
            'payment_method' => $this->when(isset($this->paymentmethod_id), function () {
                return new PaymentMethodLightResource($this->paymentmethod);
            }),
            'device' => $this->when(isset($this->device_id), function () {
                return new DeviceLightResource($this->device);
            }),
            'transaction_gateway' => $this->when(isset($this->transactiongateway_id), function () {
                return new TransactionGatewayLightResource($this->transactiongateway);
            }),
            'transaction_status' => $this->when(isset($this->transactionstatus_id), function () {
                return new TransactionStatusLightResource($this->transactionstatus);
            }),
            'description' => $this->when(isset($this->description), $this->description),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'deadline_at' => $this->when(isset($this->deadline_at), $this->deadline_at),
            'completed_at' => $this->when(isset($this->completed_at), $this->completed_at),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),
        ];
    }
}
