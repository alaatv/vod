<?php

namespace App\Traits;



use App\Models\Shahr;

trait RegionCommon
{
    public function regionMatch(int $provinceId, int $cityId)
    {
        return Shahr::where('ostan_id', $provinceId)
            ->where('id', $cityId)
            ->exists();
    }
}
