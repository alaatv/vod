<?php

namespace App\Repositories;

use App\Models\ReferralCode;
use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Support\Str;

class ReferralCodeRepo extends AlaaRepo
{

    public static function getModelClass(): string
    {
        return ReferralCode::class;
    }

    public static function findOrCreate(?User $user, int $event)
    {
        if ($user == null) {
            return null;
        }

        return ReferralCode::firstOrCreate([
            'owner_id' => $user->id,
            'event_id' => $event,
        ], [
            'code' => Str::random(10),
        ]);
    }
}
