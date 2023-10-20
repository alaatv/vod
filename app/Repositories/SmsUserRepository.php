<?php


namespace App\Repositories;

use App\Models\SmsUser;
use Illuminate\Support\Arr;

class SmsUserRepository extends AlaaRepo
{
    public static function statuses()
    {
        return static::initiateQuery()
            ->whereNotNull('status')
            ->groupBy('status')
            ->get(['status']);
    }

    public static function filter(array $filters, array $fields = [], bool $distinct = false)
    {
        $query = static::initiateQuery();

        $sms = Arr::get($filters, 'sms');
        if (is_array($sms)) {
            $query->whereIn('sms_id', $sms);
        }

        if (is_int($sms)) {
            $query->where('sms_id', $sms);
        }

        if ($distinct) {
            $query->distinct();
        }

        return $query->get($fields);
    }

    public static function getModelClass(): string
    {
        return SmsUser::class;
    }
}
