<?php


namespace App\Repositories;

use App\Models\ReferralCode;
use App\Models\ReferralCodeUser;

class NetworkMarketingRepo
{
    public static function getReferralCodeInstance(string $refCode, int $eventId)
    {
        return ReferralCode::where('code', $refCode)->where('event_id', $eventId);
    }

    public static function getReferralCodeUserInstance(int $userId, $eventId)
    {
        return ReferralCodeUser::where('user_id', $userId)->whereHas('referralCode', function ($query) use ($eventId) {
            $query->where('event_id', $eventId);
        });
    }
}
