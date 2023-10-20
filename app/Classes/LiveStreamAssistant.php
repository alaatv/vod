<?php


namespace App\Classes;


use App\Repositories\ConductorRepo;
use App\Repositories\LiveRepo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class LiveStreamAssistant
{
    /**
     * @param  string  $todayStringDate
     * @param  string  $nowTime
     */
    public static function closeFinishedPrograms(string $todayStringDate, string $nowTime): void
    {
        $finishedPrograms = ConductorRepo::getFinishedPrograms($todayStringDate, $nowTime)->get();
        foreach ($finishedPrograms as $finishedProgram) {
            $finishedProgram->update([
                'finish_time' => $finishedProgram->scheduled_finish_time,
            ]);
        }
    }

    public static function isThereLiveStream(): bool
    {
        $key = 'live:check';
        return Cache::tags(['live'])
            ->remember($key, config('constants.CACHE_5'), function () {
                $todayStringDate = Carbon::today()->setTimezone('Asia/Tehran')->toDateString();
                $live = ConductorRepo::isThereLiveStream($todayStringDate)->first();
                return (isset($live)) ? true : false;
            });
    }

    /**
     * @return Collection
     */
    public static function makeScheduleOfTheWeekCollection(): Collection
    {
        $schedules = LiveRepo::getScheduleOfTheWeek()->get();
        foreach ($schedules as $schedule) {
            $schedule->date = getCurrentWeekDateViaDayName($schedule->dayOfWeek->name);
        }
        return $schedules;
    }
}
