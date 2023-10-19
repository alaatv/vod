<?php

namespace App\Repositories;

use App\Models\Userbon;
use App\Models\Userbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class UserbonRepo extends AlaaRepo
{

    public static function getModelClass(): string
    {
        return Userbon::class;
    }

    public static function createActiveUserBon(int $userId, int $points, int $bonId)
    {
        try {
            Userbon::create([
                'bon_id' => $bonId,
                'user_id' => $userId,
                'totalNumber' => $points,
                'userbonstatus_id' => config('constants.USERBON_STATUS_ACTIVE'),
            ]);
        } catch (QueryException $e) {
            Log::channel('giveLotteryPointsErrors')->error("Error on inserting points for user {$userId}");
        }
    }
}
