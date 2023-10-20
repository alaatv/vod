<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait DateTrait
{
    /**
     * @return string
     * Converting Created_at field to jalali
     */
    public function CreatedAt_Jalali_WithTime()
    {
        $createdAt = Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->shiftTimezone('Asia/Tehran');
        $explodedDateTime = explode(' ', $createdAt->toDateTimeString());

        return $this->convertDate($createdAt, 'toJalali').' - '.$explodedDateTime[1];
    }

    public function convertDate($date, $convertType)
    {
        if (strcmp($convertType, 'toJalali') == 0 && strlen($date) > 0) {
            $explodedDate = explode(' ', $date);
            $explodedDate = $explodedDate[0];
            $explodedDate = explode('-', $explodedDate);
            $year = $explodedDate[0];
            $month = $explodedDate[1];
            $day = $explodedDate[2];

            return $this->gregorian_to_jalali($year, $month, $day, '/');
        }
        if (!(strcmp($convertType, 'toMiladi') == 0 && strlen($date) > 0)) {
            return;
        }
        $explodedDate = explode('/', $date);
        $year = $explodedDate[0];
        $month = $explodedDate[1];
        $day = $explodedDate[2];

        return $this->jalali_to_gregorian($year, $month, $day, '-');
    }

    protected function gregorian_to_jalali($g_y, $g_m, $g_d, $mod = '')
    {
        $d_4 = $g_y % 4;
        $g_a = [
            0,
            0,
            31,
            59,
            90,
            120,
            151,
            181,
            212,
            243,
            273,
            304,
            334,
        ];
        $doy_g = $g_a[(int) $g_m] + $g_d;
        if ($d_4 == 0 and $g_m > 2) {
            $doy_g++;
        }
        $d_33 = (int) ((($g_y - 16) % 132) * .0305);
        $a = ($d_33 == 3 or $d_33 < ($d_4 - 1) or $d_4 == 0) ? 286 : 287;
        $b =
            (($d_33 == 1 or $d_33 == 2) and ($d_33 == $d_4 or $d_4 == 1)) ? 78 : (($d_33 == 3 and $d_4 == 0) ? 80 : 79);
        if ((int) (($g_y - 10) / 63) == 30) {
            $a--;
            $b++;
        }
        if ($doy_g > $b) {
            $jy = $g_y - 621;
            $doy_j = $doy_g - $b;
        } else {
            $jy = $g_y - 622;
            $doy_j = $doy_g + $a;
        }
        if ($doy_j < 187) {
            $jm = (int) (($doy_j - 1) / 31);
            $jd = $doy_j - (31 * $jm++);
        } else {
            $jm = (int) (($doy_j - 187) / 30);
            $jd = $doy_j - 186 - ($jm * 30);
            $jm += 7;
        }

        return ($mod == '') ? [
            $jy,
            $jm,
            $jd,
        ] : $jy.$mod.$jm.$mod.$jd;
    }

    protected function jalali_to_gregorian($j_y, $j_m, $j_d, $mod = '')
    {
        $j_y = (int) Str::between($j_y, '-', '.');
        $d_4 = ($j_y + 1) % 4;
        $doy_j = ($j_m < 7) ? (($j_m - 1) * 31) + $j_d : (($j_m - 7) * 30) + $j_d + 186;
        $d_33 = (int) ((($j_y - 55) % 132) * .0305);
        $a = ($d_33 != 3 and $d_4 <= $d_33) ? 287 : 286;
        $b =
            (($d_33 == 1 or $d_33 == 2) and ($d_33 == $d_4 or $d_4 == 1)) ? 78 : (($d_33 == 3 and $d_4 == 0) ? 80 : 79);
        if ((int) (($j_y - 19) / 63) == 20) {
            $a--;
            $b++;
        }
        if ($doy_j <= $a) {
            $gy = $j_y + 621;
            $gd = $doy_j + $b;
        } else {
            $gy = $j_y + 622;
            $gd = $doy_j - $a;
        }
        foreach ([
                     0,
                     31,
                     ($gy % 4 == 0) ? 29 : 28,
                     31,
                     30,
                     31,
                     30,
                     31,
                     31,
                     30,
                     31,
                     30,
                     31,
                 ] as $gm => $v) {
            if ($gd <= $v) {
                break;
            }
            $gd -= $v;
        }

        return ($mod == '') ? [
            $gy,
            $gm,
            $gd,
        ] : $gy.$mod.$gm.$mod.$gd;
    }

    /**
     *  Converting Completed field to jalali
     *
     * @return string
     */
    public function CompletedAt_Jalali()
    {
        $explodedDateTime = explode(' ', $this->completed_at);

//        $explodedTime = $explodedDateTime[1] ;
        return $this->convertDate($this->completed_at, 'toJalali');
    }

    public function CompletedAt_Jalali_WithTime()
    {
        $explodedDateTime = explode(' ', $this->completed_at);

        return $this->convertDate($this->completed_at, 'toJalali').' '.$explodedDateTime[1];
    }

    /**
     * Converting validSince field to Jalali
     *
     * @param  bool  $withTime
     *
     * @return string
     */
    public function ValidSince_Jalali($withTime = true): string
    {
        $validSince = $this->validSince;
        if (empty($validSince)) {
            return '';
        }
        $explodedDateTime = explode(' ', $validSince);
        $explodedTime = Arr::get($explodedDateTime, 1);
        $explodedDate = $this->convertDate($validSince, 'toJalali');

        if (!$withTime) {
            return $explodedDate;
        }
        return ($explodedDate.' '.$explodedTime);

    }

    /**
     * Converting validUntil field to Jalali
     *
     * @param  bool  $withTime
     *
     * @return string
     */
    public function ValidUntil_Jalali($withTime = true)
    {
        $validUntil = $this->validUntil;
        if (empty($validUntil)) {
            return '';
        }
        $explodedDateTime = explode(' ', $validUntil);
        $explodedTime = $explodedDateTime[1];
        $explodedDate = $this->convertDate($validUntil, 'toJalali');

        if (!$withTime) {
            return $explodedDate;
        }
        return ($explodedDate.' '.$explodedTime);

    }

    /**
     * @return string
     * Converting Updated_at field to jalali
     */
    public function UpdatedAt_Jalali()
    {
        return $this->convertDate($this->updated_at, 'toJalali');
    }

    public function convertToJalaliDay($day)
    {
        switch ($day) {
            case 'Saturday':
                return 'شنبه';
                break;
            case 'Sunday':
                return 'یکشنبه';
                break;
            case 'Monday':
                return 'دوشنبه';
                break;
            case 'Tuesday':
                return 'سه شنبه';
                break;
            case 'Wednesday':
                return 'چهارشنبه';
                break;
            case 'Thursday':
                return 'پنجشنبه';
                break;
            case 'Friday':
                return 'جمعه';
                break;
            default:
                break;
        }
    }

    public function convertToJalaliMonth($month, $mode = 'NUMBER_TO_STRING')
    {
        if ($mode == 'NUMBER_TO_STRING') {
            $result = '';
            switch ($month) {
                case '1':
                case '01':
                    $result = 'فروردین';
                    break;
                case '2':
                case '02':
                    $result = 'اردیبهشت';
                    break;
                case '3':
                case '03':
                    $result = 'خرداد';
                    break;
                case '4':
                case '04':
                    $result = 'تیر';
                    break;
                case '5':
                case '05':
                    $result = 'مرداد';
                    break;
                case '6':
                case '06':
                    $result = 'شهریور';
                    break;
                case '7':
                case '07':
                    $result = 'مهر';
                    break;
                case '8':
                case '08':
                    $result = 'آبان';
                    break;
                case '9':
                case '09':
                    $result = 'آذر';
                    break;
                case '10':
                    $result = 'دی';
                    break;
                case '11':
                    $result = 'بهمن';
                    break;
                case '12':
                    $result = 'اسفند';
                    break;
                default:
                    break;
            }
        } else {
            if ($mode == 'STRING_TO_NUMBER') {
                $result = 0;
                switch ($month) {
                    case 'فروردین':
                        $result = 1;
                        break;
                    case 'اردیبهشت':
                        $result = 2;
                        break;
                    case 'خرداد':
                        $result = 3;
                        break;
                    case 'تیر':
                        $result = 4;
                        break;
                    case 'مرداد':
                        $result = 5;
                        break;
                    case 'شهریور':
                        $result = 6;
                        break;
                    case 'مهر':
                        $result = 7;
                        break;
                    case 'آبان':
                        $result = 8;
                        break;
                    case 'آذر':
                        $result = 9;
                        break;
                    case 'دی':
                        $result = 10;
                        break;
                    case 'بهمن':
                        $result = 11;
                        break;
                    case 'اسفند':
                        $result = 12;
                        break;
                    default:
                        break;
                }
            }
        }

        return $result;
    }

    public function getJalaliMonthDays($month)
    {
        $days = 0;
        switch ($month) {
            case 'اردیبهشت':
            case 'خرداد':
            case 'تیر':
            case 'مرداد':
            case 'شهریور':
            case 'فروردین':
                $days = 31;
                break;
            case 'آبان':
            case 'آذر':
            case 'دی':
            case 'بهمن':
            case 'مهر':
                $days = 30;
                break;
            case 'اسفند':
                $days = 29;
                break;
            default:

                break;
        }

        return $days;
    }

    public function getFinancialYear($currentYear, $currentMonth, $pointedMonth)
    {
        $pointedYear = 0;
        if ($currentMonth <= 6) {
            if ($pointedMonth <= 6) {
                $pointedYear = $currentYear;
            } else {
                $pointedYear = $currentYear - 1;
            }
        } else {
            if ($pointedMonth <= 6) {
                $pointedYear = $currentYear - 1;
            } else {
                $pointedYear = $currentYear;
            }
        }

        return $pointedYear;
    }

    public function getDateTimeAttribute($value)
    {
        $date = $this->CreatedAt_Jalali();
        $time = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)
            ->setTimezone('Asia/Tehran')
            ->format('H:i');

        return $date.' ساعت '.$time;
    }

    /**
     * @return string
     * Converting Created_at field to jalali
     */
    public function CreatedAt_Jalali()
    {
        return $this->convertDate($this->created_at, 'toJalali');
    }

    public function nowTimestamp(): string
    {
        return now()->format('YmdHis').substr(hrtime(true), 0, 6);
    }

    public function convertTimeToTehran(string $time): string
    {
        $time = \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $time)
            ->setTimezone('Asia/Tehran')
            ->format('H:i');

        return $time;
    }

    /**
     * @param  Carbon  $currentGregorianDate
     *
     * @return array
     */
    protected function todayJalaliSplittedDate(Carbon $currentGregorianDate): array
    {
        $delimiter = '/';
        $currentJalaliDate = $this->gregorian_to_jalali($currentGregorianDate->year, $currentGregorianDate->month,
            $currentGregorianDate->day, $delimiter);
        $currentJalaliDateSplit = explode($delimiter, $currentJalaliDate);
        $currentJalaliYear = $currentJalaliDateSplit[0];
        $currentJalaliMonth = $currentJalaliDateSplit[1];
        $currentJalaliDay = $currentJalaliDateSplit[2];
        return [$currentJalaliYear, $currentJalaliMonth, $currentJalaliDay];
    }
}
