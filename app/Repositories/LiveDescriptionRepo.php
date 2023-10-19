<?php

namespace App\Repositories;

use App\Models\LiveDescription;
use App\Models\LiveDescription;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB;

class LiveDescriptionRepo
{

    public static function increaseSeenCounter(LiveDescription $liveDescription)
    {
        try {
            DB::transaction(function () use ($liveDescription) {
                $liveDescription->users()->attach(auth('api')->user()->id, ['seen_at' => now()]);
                $liveDescription->seen_counter += 1;
                $liveDescription->save();
            });
        } catch (QueryException $e) {

        }
    }
}
