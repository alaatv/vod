<?php

namespace App\Repositories;

use App\Models\SMS;
use App\Models\SMS;
use Illuminate\Support\Arr;

class SMSRepository extends AlaaRepo
{

    public static function getModelClass(): string
    {
        return SMS::class;
    }

    public static function filter(
        array $filters,
        array $fields = [],
        array $disableAppends = [
            'recheck_sms_status_link', 'sms_recipients_link', 'resend_unsuccessful_bulk_sms_link', 'detail'
        ]
    ) {
        $query = static::initiateQuery();

        if ($foreign_id = Arr::get($filters, 'foreign_id')) {
            $query->where('foreign_id', 'like', $foreign_id);
        }

        if ($foreign_type = Arr::get($filters, 'foreign_type')) {
            $query->where('foreign_type', 'like', $foreign_type);
        }

        if ($pattern_code = Arr::get($filters, 'pattern_code')) {
            $query->whereHas('details', function ($query) use ($pattern_code) {
                $query->where('pattern_code', $pattern_code);
            });
        }

        return $query->get($fields)
            ->makeHidden($disableAppends);

    }
}
