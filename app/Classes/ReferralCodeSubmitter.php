<?php

namespace App\Classes;

use App\Models\Order;
use App\Models\ReferralCode;

class ReferralCodeSubmitter
{
    private Order $order;

    /**
     * CouponSubmitter constructor.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param  ReferralCode  $referralCode
     * @return bool
     */
    public function submit(ReferralCode $referralCode): bool
    {
        $oldReferralCode = $this->order->referralCode;
        if (!isset($oldReferralCode)) {
            $referralCode->increaseUseNumber()->update();
            $orderUpdateResult = $this->order->attachReferralCode($referralCode)->updateWithoutTimestamp();
            if ($orderUpdateResult) {
                return true;
            }
            $referralCode->decreaseUseNumber()->update();
            return false;
        }

        if ($oldReferralCode->id == $referralCode->id) {
            return true;
        }

        $oldReferralCode->decreaseUseNumber()->update();
        $referralCode->increaseUseNumber()->update();
        $orderUpdateResult = $this->order->attachReferralCode($referralCode)->updateWithoutTimestamp();
        if ($orderUpdateResult) {
            return true;
        }

        $oldReferralCode->increaseUseNumber()->update();
        $referralCode->decreaseUseNumber()->update();

        return false;
    }
}
