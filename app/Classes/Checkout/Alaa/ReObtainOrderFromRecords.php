<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/4/2018
 * Time: 3:56 PM
 */

namespace App\Classes\Checkout\Alaa;

use App\Classes\Abstracts\Checkout\Cashier;
use App\Classes\Abstracts\Checkout\CheckoutInvoker;
use App\Models\Order;

class ReObtainOrderFromRecords extends CheckoutInvoker
{
    private $order;

    private $totalRawPriceWhichHasDiscount;

    private $totalRawPriceWhichDoesntHaveDiscount;

    private $couponDiscountType;

    private $orderDiscount;

    /**
     * OrderCheckout constructor.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->totalRawPriceWhichHasDiscount = isset($order->cost) ? $order->cost : 0;
        $this->totalRawPriceWhichDoesntHaveDiscount = isset($order->costwithoutcoupon) ? $order->costwithoutcoupon : 0;
        $this->couponDiscountType = $order->coupon_discount_type;
        $this->orderDiscount = $order->discount;
    }

    public function getChainClassesNameSpace(): string
    {
        return __NAMESPACE__."\\Chains";
    }

    protected function fillChainArray(): array
    {
        $chainCells = [
            'AlaaOrderCouponCalculatorBasedOnPercentage',
            'AlaaOrderCouponCalculatorBasedOnCostAmount',
            'AlaaOrderPriceCalculator',
            'AlaaOrderDiscountCostAmountCalculator',
        ];
        if (isset($this->order->referralCode)) {
            $chainCells[] = 'AlaaOrderReferralCodeCalculator';
        }
        return $chainCells;

    }

    protected function initiateCashier(): Cashier
    {
        $alaaCashier = new AlaaCashier();

        $couponDiscountCostAmount = 0;
        $couponDiscountPercentage = 0;
        $couponType = $this->order->coupon_discount_type;
        if ($couponType !== false) {
            if ($couponType['type'] == config('constants.DISCOUNT_TYPE_PERCENTAGE')) {
                $couponDiscountPercentage = $couponType['discount'] / 100;
            } else {
                if ($couponType['type'] == config('constants.DISCOUNT_TYPE_COST')) {
                    $couponDiscountCostAmount = $couponType['discount'];
                }
            }
        }

        $alaaCashier->setOrder($this->order)
            ->setTotalRawPriceWhichHasDiscount($this->totalRawPriceWhichHasDiscount)
            ->setTotalRawPriceWhichDoesntHaveDiscount($this->totalRawPriceWhichDoesntHaveDiscount)
            ->setOrderCouponDiscountPercentage($couponDiscountPercentage)
            ->setOrderCouponDiscountCostAmount($couponDiscountCostAmount)
            ->setOrderDiscountCostAmount($this->orderDiscount);

        if (isset($this->order->referralCode)) {
            $referralCodeDiscountType = $this->order->referralCode->referralRequest->discountType->id;
            $referralCodeDiscount = $this->order->referralCode->referralRequest->discount;
            $alaaCashier->setOrderReferralCodeDiscount($referralCodeDiscount)
                ->setOrderReferralCodeDiscountType($referralCodeDiscountType);
        }

        return $alaaCashier;
    }
}
