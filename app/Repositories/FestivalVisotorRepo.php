<?php

namespace App\Repositories;

use App\Models\TempFestivalVisits;
use App\Models\TempFestivalVisits;

class FestivalVisotorRepo
{

    public static function findVisitorByMobile($mobile)
    {
        return TempFestivalVisits::where('mobile', $mobile)->first() ?? null;
    }
}
