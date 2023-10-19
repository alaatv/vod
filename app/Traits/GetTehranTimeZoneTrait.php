<?php


namespace App\Traits;


use Carbon\Carbon;

trait GetTehranTimeZoneTrait
{
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Tehran');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Tehran');
    }
}
