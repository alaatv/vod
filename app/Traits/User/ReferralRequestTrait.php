<?php

namespace App\Traits\User;


use App\Models\ReferralCode;
use App\Models\ReferralRequest;

trait ReferralRequestTrait
{
    public function createReferralRequest(User $user, $discounttype_id, $number_of_codes, $commission)
    {
        return $user->referralRequests()->create([
            'discounttype_id' => $discounttype_id,
            'discount' => config('constants.REFERRAL_CODE_DISCOUNT'),
            'numberOfCodes' => $number_of_codes,
            'usageLimit' => 1,
            'default_commission' => $commission,
        ]);
    }

    public function createReferralCodes(ReferralRequest $referralRequest, int $numberOfCodes)
    {
        $referralRequest->referralCodes()->createMany($this->generateReferralCodes($referralRequest->owner_id,
            $numberOfCodes));
    }

    private function generateReferralCodes($owner_id, $numberOfCodes)
    {
        $referralCodes = [];
        for ($i = 0; $i < $numberOfCodes; $i++) {
            $referralCodes[] = [
                'owner_id' => $owner_id,
                'code' => $this->generateCode(),
                'enable' => 1,
            ];
        }
        return $referralCodes;
    }

    private function generateCode()
    {
        $code = randomNumber(2).'-'.randomNumber(5);
        $isUsed = ReferralCode::where('code', $code)->first();
        if ($isUsed) {
            return $this->generateCode();
        }
        return $code;
    }

}
