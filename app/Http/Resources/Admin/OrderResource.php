<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\Coupon;
use App\Http\Resources\OrderOwner;
use App\Http\Resources\Orderpostinginfo;
use App\Http\Resources\Orderstatus;
use App\Http\Resources\Paymentstatus;
use App\Http\Resources\PendingTransaction;
use App\Http\Resources\PurchasedOrderproduct;
use App\Http\Resources\SuccessfulTransaction;
use App\Http\Resources\UnpaidTransaction;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Class OrderResource
 *
 * @mixin Order
 * */
class OrderResource extends AlaaJsonResource
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
        if (!($this->resource instanceof Order)) {
            return [];
        }

        $this->loadMissing('orderstatus', 'paymentstatus', 'orderproducts', 'transactions', 'orderpostinginfos',
            'user', /*'inserter'*/);

        return [
            'id' => $this->id,
            'discount' => $this->discount,
            'customer_description' => $this->when(isset($this->customerDescription), $this->customerDescription),
            // 'customer_extra_info' => $this->customerExtraInfo ,
            'price' => $this->price,
            'paid_price' => $this->paid_price,
            'refund_price' => $this->refund_price,
            'debt' => $this->debt,
            'orderstatus' => $this->when(isset($this->orderstatus_id), function () {
                return new Orderstatus($this->orderstatus);
            }),
            'paymentstatus' => $this->when(isset($this->paymentstatus_id), function () {
                return new Paymentstatus($this->paymentstatus);
            }),
            'orderproducts' => $this->when(isset($this->orderproducts), function () {
                return PurchasedOrderproduct::collection($this->whenLoaded('orderproducts'));
            }),
            'coupon_info' => $this->when(isset($this->coupon_id), function () {
                return new Coupon($this->coupon);
            }),
            // TODO: Why in the following 4 cases, each condition has been checked twice? for example "$this->successful_transactions->isNotEmpty()"
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
            'user' => $this->when(isset($this->user_id), function () {
                return new OrderOwner($this->user);
            }),
            'created_at' => $this->when(isset($this->created_at), function () {
                return optional($this->created_at)->toDateTimeString();
            }),
            'completed_at' => $this->when(isset($this->completed_at), $this->completed_at),
            'updated_at' => $this->when(isset($this->updated_at), function () {
                return optional($this->updated_at)->toDateTimeString();
            }),

//            'inserter' => $this->when(isset($this->insertor_id), function () {
//                // TODO: I don't know which resource to use for the inserter.
//                return new UserLightResource($this->inserter);
//            }),
            'coupon_discount' => $this->couponDiscount,
            'coupon_discount_amount' => $this->couponDiscountAmount,
            'cost' => $this->when(isset($this->cost), $this->cost),
            'cost_without_coupon' => $this->when(isset($this->costwithoutcoupon), $this->costwithoutcoupon),
            'checkout_date_time' => $this->when(isset($this->checkOutDateTime), $this->checkOutDateTime),
            'edit_link' => route('order.edit', $this->id),
        ];
    }
}
