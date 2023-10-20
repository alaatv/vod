<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class Order
 *
 * @mixin \App\Order
 * */
class Order extends AlaaJsonResource
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
        if (!($this->resource instanceof \App\Order)) {
            return [];
        }

        $this->loadMissing('orderstatus', 'paymentstatus', 'orderproducts', 'transactions', 'orderpostinginfos',
            'user');

        return [
            'id' => $this->id,
            'discount' => $this->discount,
            'customer_description' => $this->when(isset($this->customerDescription), $this->customerDescription),
//            'customer_extra_info'      => $this->customerExtraInfo ,
            'price' => $this->price,
            'paid_price' => $this->paid_price,
            'refund_price' => $this->refund_price,
            'debt' => $this->debt,
            'orderstatus' => $this->when(isset($this->orderstatus_id), function () {
                return isset($this->orderstatus_id) ? new Orderstatus($this->orderstatus) : null;
            }),
            'paymentstatus' => $this->when(isset($this->paymentstatus_id), function () {
                return isset($this->paymentstatus_id) ? new Paymentstatus($this->paymentstatus) : null;
            }),
//            'orderproducts'           => Orderproduct::collection($this->whenLoaded('orderproducts')),
            'orderproducts' => $this->when(isset($this->orderproducts), function () {
                return isset($this->orderproducts) ? PurchasedOrderproduct::collection($this->whenLoaded('orderproducts')) : null;
            }),
//            'coupon_info'              => $this->when(!is_null($this->coupon_info) , $this->coupon_info), //Deprecated
            'coupon_info' => $this->when(isset($this->coupon_id), function () {
                return isset($this->coupon_id) ? new Coupon($this->coupon) : null;
            }),
            'successful_transactions' => $this->when($this->successful_transactions->isNotEmpty(), function () {
                return $this->successful_transactions->isNotEmpty() ? SuccessfulTransaction::collection($this->successful_transactions) : null;
            }), // It is not a relationship
            'pending_transactions' => $this->when($this->pending_transactions->isNotEmpty(), function () {
                return $this->pending_transactions->isNotEmpty() ? PendingTransaction::collection($this->pending_transactions) : null;
            }), // It is not a relationship
            'unpaid_transaction' => $this->when($this->unpaid_transactions->isNotEmpty(), function () {
                return $this->unpaid_transactions->isNotEmpty() ? UnpaidTransaction::collection($this->unpaid_transactions) : null;
            }), // It is not a relationship
            'posting_info' => $this->when($this->orderpostinginfos->isNotEmpty(), function () {
                return $this->orderpostinginfos->isNotEmpty() ? Orderpostinginfo::collection($this->whenLoaded('orderpostinginfos')) : null;
            }),
//            'usedBonSum'             => $this->used_bon_sum,
//            'addedBonSum'            => $this->added_bon_sum,
            'user' => $this->when(isset($this->user_id), function () {
                return isset($this->user_id) ? new OrderOwner($this->user) : null;
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'completed_at' => $this->when(isset($this->completed_at), function () {
                return isset($this->completed_at) ? $this->completed_at : null;
            }),
        ];
    }
}
