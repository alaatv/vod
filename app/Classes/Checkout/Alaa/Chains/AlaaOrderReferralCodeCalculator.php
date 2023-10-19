<?php
/**
 * Created by PhpStorm.
 * User: mohamamad
 * Date: 12/2/2018
 * Time: 11:16 AM
 */

namespace App\Classes\Checkout\Alaa\Chains;

use App\Classes\Abstracts\Checkout\OrderReferralCodeCalculator;

class AlaaOrderReferralCodeCalculator extends OrderReferralCodeCalculator
{
    protected function calculateReferralCodeDiscount(
        $temporaryFinalPrice,
        $referralCodeDiscount,
        $referralCodediscountType
    ) {
        if (is_null($referralCodeDiscount)) {
            return $temporaryFinalPrice;
        }

        if ($referralCodediscountType == config('constants.DISCOUNT_TYPE_PERCENTAGE')) {
            $referralCodeDiscount = ($temporaryFinalPrice * $referralCodeDiscount) / 100;
        }
        return max($temporaryFinalPrice - $referralCodeDiscount, 0);
    }
}
