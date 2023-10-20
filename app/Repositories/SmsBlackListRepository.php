<?php

namespace App\Repositories;

use App\Models\SmsBlackList;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class SmsBlackListRepository extends AlaaRepo
{

    public static function getModelClass(): string
    {
        return SmsBlackList::class;
    }

    public static function getBlockedList(): Builder
    {
        return static::initiateQuery();
    }

    public static function create(array $data)
    {
        try {
            SmsBlackList::query()->firstOrCreate($data, $data);
        } catch (Exception $exception) {
            $errorMessage = 'could create blacklist item with data (reason: '.$exception->getMessage().')';
            Log::error($errorMessage, $data);
        }
    }
}
