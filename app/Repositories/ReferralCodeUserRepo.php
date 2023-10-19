<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\ReferralCode;
use App\Models\ReferralCode;
use App\Models\ReferralCodeUser;
use App\Models\ReferralCodeUser;

class ReferralCodeUserRepo
{
    public static function create(Order $order, ReferralCode $referralCode)
    {
        return ReferralCodeUser::Create([
            'user_id' => $order->user->id,
            'code_id' => $referralCode->id,
            'subject_id' => $order->id,
            'subject_type' => Order::class
        ]);
    }
}
