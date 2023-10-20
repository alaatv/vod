<?php

namespace App\Models;

use Carbon\Carbon;

class Employeetimesheet extends BaseModel
{
    protected $fillable = [
        'user_id',
        'date',
        'userBeginTime',
        'userFinishTime',
        'allowedLunchBreakInSec',
        'clockIn',
        'beginLunchBreak',
        'finishLunchBreak',
        'clockOut',
        'breakDurationInSeconds',
        'workdaytype_id',
        'isPaid',
        'managerComment',
        'employeeComment',
        'modifier_id',
        'overtime_status_id',
        'timeSheetLock',
    ];

    /**
     * Set the employeeTimeSheet's clockIn.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setClockinAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) == 0) {
            return null;
        }
        $value = explode(':', $value);

        $hour = $value[0];
        if (strlen($hour) == 0) {
            $hour = '00';
        }

        $minute = $value[1];
        if (strlen($minute) == 0) {
            $minute = '00';
        }

        $this->attributes['clockIn'] = $hour.':'.$minute;
    }

    /**
     * Set the employeeTimeSheet's clockIn.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setBeginlunchbreakAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) == 0) {
            return;
        }
        $value = explode(':', $value);

        $hour = $value[0];
        if (strlen($hour) == 0) {
            $hour = '00';
        }

        $minute = $value[1];
        if (strlen($minute) == 0) {
            $minute = '00';
        }

        $this->attributes['beginLunchBreak'] = $hour.':'.$minute;

    }

    /**
     * Set the employeeTimeSheet's clockIn.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setFinishlunchbreakAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) == 0) {
            return;
        }
        $value = explode(':', $value);

        $hour = $value[0];
        if (strlen($hour) == 0) {
            $hour = '00';
        }

        $minute = $value[1];
        if (strlen($minute) == 0) {
            $minute = '00';
        }

        $this->attributes['finishLunchBreak'] = $hour.':'.$minute;

    }

    /**
     * Set the employeeTimeSheet's clockIn.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setClockoutAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) == 0) {
            return;
        }
        $value = explode(':', $value);

        $hour = $value[0];
        if (strlen($hour) == 0) {
            $hour = '00';
        }

        $minute = $value[1];
        if (strlen($minute) == 0) {
            $minute = '00';
        }

        $this->attributes['clockOut'] = $hour.':'.$minute;

    }

    /**
     * Set the employeeTimeSheet's userBeginTime.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setUserbegintimeAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) == 0) {
            return;
        }
        $value = explode(':', $value);

        $hour = $value[0];
        if (strlen($hour) == 0) {
            $hour = '00';
        }

        $minute = $value[1];
        if (strlen($minute) == 0) {
            $minute = '00';
        }

        $this->attributes['userBeginTime'] = $hour.':'.$minute;

    }

    /**
     * Set the employeeTimeSheet's userFinishTime.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setUserfinishtimeAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) == 0) {
            return;
        }
        $value = explode(':', $value);

        $hour = $value[0];
        if (strlen($hour) == 0) {
            $hour = '00';
        }

        $minute = $value[1];
        if (strlen($minute) == 0) {
            $minute = '00';
        }

        $this->attributes['userFinishTime'] = $hour.':'.$minute;

    }

    /**
     * Set the employeeTimeSheet's breakDurationInSeconds.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setBreakdurationinsecondsAttribute($value)
    {
        if (strcmp(gettype($value), 'integer') == 0) {
            $this->attributes['breakDurationInSeconds'] = $value;
        } else {
            if (strlen(preg_replace('/\s+/', '', $value)) != 0) {
                $breakTime = explode(':', $value);
                $this->attributes['breakDurationInSeconds'] = $breakTime[0] * 3600 + $breakTime[1] * 60;
            }
        }
    }

    /**
     * Set the employeeTimeSheet's managerComment.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setManagercommentAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) != 0) {
            $this->attributes['managerComment'] = $value;
        } else {
            $this->attributes['managerComment'] = null;
        }
    }

    /**
     * Set the employeeTimeSheet's employeeComment.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setEmployeecommentAttribute($value)
    {
        if (strlen(preg_replace('/\s+/', '', $value)) != 0) {
            $this->attributes['employeeComment'] = $value;
        } else {
            $this->attributes['managerComment'] = null;
        }
    }

    /**
     * Set the employeeTimeSheet's date.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::parse($value)
            ->format('Y-m-d');
    }

    /**
     * Set the employeeTimeSheet's breakDurationInSeconds.
     *
     * @param  string  $value
     *
     * @return void
     */
    public function setAllowedlunchbreakinsecAttribute($value)
    {
        if (strcmp(gettype($value), 'integer') == 0) {
            $this->attributes['allowedLunchBreakInSec'] = $value;
        } else {
            if (strlen(preg_replace('/\s+/', '', $value)) != 0) {
                $breakTime = explode(':', $value);
                $this->attributes['allowedLunchBreakInSec'] = $breakTime[0] * 3600 + $breakTime[1] * 60;
            }
        }
    }

    /**
     * Get the Employeeschedule's userBeginTime.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getUserbegintimeAttribute($value)
    {
        return isset($value) ? $value : '00:00:00';
    }

    /**
     * Get the Employeeschedule's usrFinishTime.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getUserfinishtimeAttribute($value)
    {
        return isset($value) ? $value : '00:00:00';
    }

    /**
     * Get the Employeeschedule's clockIn.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getClockinAttribute($value)
    {
        return isset($value) ? $value : '00:00:00';
    }

    /**
     * Get the Employeeschedule's date.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getBeginlunchbreakAttribute($value)
    {
        return isset($value) ? $value : '00:00:00';
    }

    /**
     * Get the Employeeschedule's beginLunchBreak.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getFinishlunchbreakAttribute($value)
    {
        return isset($value) ? $value : '00:00:00';
    }

    /**
     * Get the Employeeschedule's clockOut.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getClockoutAttribute($value)
    {
        return isset($value) ? $value : '00:00:00';
    }

    /**
     * Get the employeeTimeSheet's breakDurationInSeconds.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getAllowedlunchbreakinsecAttribute($value)
    {
        return isset($value) ? gmdate('H:i:s', $value) : null;
    }

    /**
     * Get the Employeeschedule's lunchBreakInSeconds.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getBreakdurationinsecondsAttribute($value)
    {
        return gmdate('H:i:s', $value);
    }

    /**
     * Get the Employeeschedule's timeSheetLock.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getTimesheetlockAttribute($value)
    {
        return $value ? 'بله' : 'خیر';
    }

    /**
     * Get the Employeeschedule's isPaid.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getIspaidAttribute($value)
    {
        return $value ? 'بله' : 'خیر';
    }

    /**
     * Get the Employeeschedule's updated_at.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getUpdatedAtAttribute($value)
    {
        /*$explodedDateTime = explode(" ", $value);*/
        /*$explodedTime = $explodedDateTime[1] ;*/
        return $this->convertDate($value, 'toJalali');
    }

    /**
     * Get the Employeeschedule's created_at.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        /*$explodedDateTime = explode(" ", $value);*/
        /*$explodedTime = $explodedDateTime[1] ;*/
        return $this->convertDate($value, 'toJalali');
    }

    /**
     * Get the Employeeschedule's date.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getDateAttribute($value)
    {
        return $this->convertDate($value, 'toJalali');
    }

    /**
     * Get the Employeeschedule's date.
     *
     * @param $mode
     *
     * @return string
     */
    public function getDate($mode)
    {
        switch ($mode) {
            case 'WEEK_DAY':
                return $this->convertToJalaliDay(Carbon::parse($this->getRawOriginal('date'))
                    ->format('l'));
            default:
                return null;
        }
    }

    public function getEmployeeFullName()
    {
        $fullName = '';
        if (isset($this->user->id) && isset($this->user->firstName[0])) {
            $fullName .= $this->user->firstName;
        }
        if (!(isset($this->user->id) && isset($this->user->lastName[0]))) {

            return $fullName;
        }
        if (strlen($fullName) > 0) {
            $fullName .= ' '.$this->user->lastName;
        } else {
            $fullName .= $this->user->lastName;
        }


        return $fullName;
    }

    public function getModifierFullName()
    {
        return $this->modifier->firstName.' '.$this->modifier->lastName;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function modifier()
    {
        return $this->belongsTo(User::class);
    }

    public function workdaytype()
    {
        return $this->belongsTo(Workdaytype::class);
    }

    public function overtimestatus()
    {
        return $this->belongsTo(Employeeovertimestatus::Class, 'id', 'overtime_status_id');
    }

    public function getObtainWorkAndShiftDiffInHourAttribute()
    {
        return $this->obtainWorkAndShiftDiff('HOUR_FORMAT');
    }

    /**
     * Obtain the employeeTimeSheet workAndShiftDiff
     *
     * @param  string  $mode
     *
     * @return bool|false|int|string
     */
    public function obtainWorkAndShiftDiff($mode = 'IN_SECONDS')
    {
        switch ($mode) {
            case 'IN_SECONDS' :
                if (!($this->obtainRealWorkTime('HOUR_FORMAT') !== false && $this->obtainShiftTime() !== false)) {
                    return false;
                }
                $beginTime = Carbon::parse($this->obtainRealWorkTime('HOUR_FORMAT'));
                $finishTime = Carbon::parse($this->obtainShiftTime());
                $workAndShiftDiff = $finishTime->diffInSeconds($beginTime, false);
                return $workAndShiftDiff;
            case 'HOUR_FORMAT':
                if (!($this->obtainRealWorkTime('HOUR_FORMAT') !== false && $this->obtainShiftTime() !== false)) {
                    return false;
                }
                $beginTime = Carbon::parse($this->obtainRealWorkTime('HOUR_FORMAT'));
                $finishTime = Carbon::parse($this->obtainShiftTime());
                $workAndShiftDiff = $finishTime->diffInSeconds($beginTime, false);
                if ($workAndShiftDiff < 0) {
                    return gmdate('H:i', abs($workAndShiftDiff)).' منفی';
                }
                return gmdate('H:i', abs($workAndShiftDiff));
            case 'PERSIAN_FORMAT':
                if (!($this->obtainRealWorkTime('HOUR_FORMAT') !== false && $this->obtainShiftTime() !== false)) {
                    return false;
                }
                $beginTime = Carbon::parse($this->obtainRealWorkTime('HOUR_FORMAT'));
                $finishTime = Carbon::parse($this->obtainShiftTime());
                $workAndShiftDiffInSec = $finishTime->diffInSeconds($beginTime, false);
                $workAndShiftDiff = gmdate('H:i:s', abs($workAndShiftDiffInSec));
                $shiftTime = explode(':', $workAndShiftDiff);
                $hour = $shiftTime[0];
                $minute = $shiftTime[1];
                $second = $shiftTime[2];
                $persianTime = '';
                if ($hour > 0) {
                    $persianTime .= $hour.' ساعت ';
                }
                if ($minute > 0) {
                    $persianTime .= $minute.' دقیقه ';
                }
                if ($second > 0) {
                    $persianTime .= $second.' ثانیه ';
                }

                if ($workAndShiftDiffInSec < 0) {
                    $persianTime .= ' کم کاری';
                } else {
                    $persianTime .= ' اضافه کاری';
                }

                return $persianTime;
            default:
                return false;
        }
    }

    /**
     * Obtain the employeeTimeSheet realWorkTime
     *
     * @param  string  $mode
     *
     * @return bool|int|string
     */
    public function obtainRealWorkTime($mode = 'IN_SECONDS')
    {
        switch ($mode) {
            case 'IN_SECONDS':
                if (!(isset($this->allowedLunchBreakInSec) && $this->obtainWorkTime() !== false && $this->obtainTotalBreakTime() !== false)) {
                    return false;
                }
                $beginTime = Carbon::parse($this->obtainWorkTime());
                $finishTime = Carbon::parse($this->obtainTotalBreakTime());
                $realWorkTime = $finishTime->diffInSeconds($beginTime);

                return $realWorkTime;
            case 'HOUR_FORMAT':
                if (!(isset($this->allowedLunchBreakInSec) && $this->obtainWorkTime() !== false && $this->obtainTotalBreakTime() !== false)) {
                    return false;
                }
                $beginTime = Carbon::parse($this->obtainWorkTime());
                $finishTime = Carbon::parse($this->obtainTotalBreakTime());
                $realWorkTime = $finishTime->diff($beginTime)->format('%H:%I:%S');

                return $realWorkTime;
            default:
                return false;
        }
    }

    /**
     * Obtain the employeeTimeSheet workTime
     */
    public function obtainWorkTime()
    {
        if (!(isset($this->clockIn) && isset($this->clockOut))) {
            return false;
        }
        $beginTime = Carbon::parse($this->clockIn);
        $finishTime = Carbon::parse($this->clockOut);
        $workTime = $finishTime->diff($beginTime)->format('%H:%I:%S');

        return $workTime;

    }

    /**
     * Obtain the employeeTimeSheet totalBreakTime
     */
    public function obtainTotalBreakTime()
    {
        $lunchOverTime = $this->obtainLunchOverTimeInSec();
        if ($lunchOverTime === false) {
            return false;
        }
        if ($lunchOverTime < 0) {
            $totalBreak = $this->getRawOriginal('breakDurationInSeconds') + abs($lunchOverTime);
        } else {
            $totalBreak = $this->getRawOriginal('breakDurationInSeconds');
        }

        return gmdate('H:i:s', $totalBreak);
    }

    /**
     * Obtain the employeeTimeSheet lunchOverTimeInSeconds
     */
    public function obtainLunchOverTimeInSec()
    {
        if (!(isset($this->allowedLunchBreakInSec) && $this->obtainLunchTime() !== false)) {
            return false;
        }
        $beginTime = Carbon::parse($this->allowedLunchBreakInSec);
        $finishTime = Carbon::parse($this->obtainLunchTime());

        return $finishTime->diffInSeconds($beginTime, false);
    }

    /**
     * Obtain the employeeTimeSheet lunchTime
     */
    public function obtainLunchTime()
    {
        if (!(isset($this->beginLunchBreak) && isset($this->finishLunchBreak))) {
            return false;
        }
        $beginTime = Carbon::parse($this->beginLunchBreak);
        $finishTime = Carbon::parse($this->finishLunchBreak);
        $lunchTime = $finishTime->diff($beginTime)->format('%H:%I:%S');

        return $lunchTime;
    }

    /**
     * Obtain the employeeTimeSheet shiftTime
     *
     * @param  string  $mode
     *
     * @return array|bool|int|string
     */
    public function obtainShiftTime($mode = 'HOUR_FORMAT')
    {
        switch ($mode) {
            case 'IN_SECONDS':
                if (!(isset($this->userBeginTime) && isset($this->userFinishTime))) {
                    return false;
                }
                $beginTime = Carbon::parse($this->userBeginTime);
                $finishTime = Carbon::parse($this->userFinishTime);
                $shiftTime = $finishTime->diffInSeconds($beginTime, false);

                return $shiftTime;
            case 'HOUR_FORMAT':
                if (!(isset($this->userBeginTime) && isset($this->userFinishTime))) {
                    return false;
                }
                $beginTime = Carbon::parse($this->userBeginTime);
                $finishTime = Carbon::parse($this->userFinishTime);
                $shiftTime = $finishTime->diff($beginTime)->format('%H:%I:%S');

                return $shiftTime;
            case 'PERSIAN_FORMAT':
                if (!(isset($this->userBeginTime) && isset($this->userFinishTime))) {
                    return false;
                }
                $beginTime = Carbon::parse($this->userBeginTime);
                $finishTime = Carbon::parse($this->userFinishTime);
                $shiftTime = $finishTime->diff($beginTime)
                    ->format('%H:%I:%S');
                if (strcmp($shiftTime, '00:00:00') == 0) {
                    $persianShiftTime = 0;
                } else {
                    $shiftTime = explode(':', $shiftTime);
                    $hour = $shiftTime[0];
                    $minute = $shiftTime[1];
                    $second = $shiftTime[2];
                    $persianShiftTime = '';
                    if ($hour > 0) {
                        $persianShiftTime .= $hour.' ساعت ';
                    }
                    if ($minute > 0) {
                        $persianShiftTime .= $minute.' دقیقه ';
                    }
                    if ($second > 0) {
                        $persianShiftTime .= $second.' ثانیه ';
                    }
                }

                return $persianShiftTime;
            default:
                return false;
                break;
        }
    }
}
