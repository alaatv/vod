<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Employeeschedule extends BaseModel
{
    public const LUNCH_BREAK_IN_SECONDS = 2400;

    protected $fillable = [
        'user_id',
        'day_id',
        'beginTime',
        'finishTime',
        'lunchBreakInSeconds',
    ];

    protected $appends = ['day'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dayOfWeek()
    {
        return $this->belongsTo(Dayofweek::class, 'day_id');
    }

    public function employeetimesheets()
    {
        return $this->hasMany(Employeetimesheet::class);
    }

    public function scopeDayId($query, int $dayId)
    {
        $query->where('day_id', $dayId);
    }

    /**
     * Get the Employeeschedule's beginTime.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getBegintimeAttribute($value)
    {
        $time = new Carbon($value);
        $time = $time->format('H:i');

        return $time.' '.$this->day;
    }

    public function getDayAttribute(): ?string
    {
        return Cache::tags(['employeeSchedule', 'dayOfWeek'])
            ->remember('employeeSchedule:getDayAttribute:'.$this->id, config('constants.CACHE_600'), function () {
                $dayOfWeek = Dayofweek::find($this->day_id);

                return $dayOfWeek?->display_name;
            });
    }

    /**
     * Get the Employeeschedule's finishTime.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getFinishtimeAttribute($value)
    {
        $time = new Carbon($value);
        $time = $time->format('H:i');

        return $time.' '.$this->day;
    }

    // TODO: The gmdate method returns the correct answer in less than 24 hours. Based on this method, I created
    //  the secondsToHumanFormat method in the helpers.php that we can use instead. Please check it.
    /**
     * Get the Employeeschedule's lunchBreakInSeconds.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getLunchbreakinsecondsAttribute($value)
    {
        return gmdate('H:i:s', $value);
    }
}
