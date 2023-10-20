<?php


namespace App\Repositories;


use App\Models\Dayofweek;
use App\Models\Live;
use Illuminate\Database\Eloquent\Builder;

class LiveRepo
{
    /**
     * @param  Dayofweek  $dayOfWeek
     * @param  string  $todayDate
     * @param  string  $nowTime
     *
     * @return Builder
     */
    public static function isThereScheduledProgram(Dayofweek $dayOfWeek, string $todayDate, string $nowTime): Builder
    {
        return Live::where('dayofweek_id', $dayOfWeek->id)
            ->where('enable', 1)
            ->where('first_live', '<=', $todayDate)
            ->where('last_live', '>=', $todayDate)
            ->where('start_time', '<=', $nowTime)
            ->where('finish_time', '>=', $nowTime);
    }

    /**
     * @return Builder
     */
    public static function getScheduleOfTheWeek(): Builder
    {
        return Live::enable()->with('dayOfWeek')->orderBy('dayofweek_id');
    }

}
