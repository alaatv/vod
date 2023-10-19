<?php

namespace App\Repositories;

use App\Models\Report;
use App\Models\User;
use App\Repositories\Loging\ActivityLogRepo;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ReportRepository extends AlaaRepo
{
    public static function getModelClass(): string
    {
        return Report::class;
    }

    public static function filter(array $data = [], array $relations = []): Builder
    {
        $query = self::initiateQuery()->with($relations);

        if (empty($data)) {
            return $query;
        }

        if ($gateway = self::setFilterField($data, 'gateway')) {
            $query->where('data->gateway_id', $gateway);
        }

        if ($reportStatus = self::setFilterField($data, 'report_status')) {
            $query->where('status_id', $reportStatus);
        }

        if ($type = self::setFilterField($data, 'type')) {
            $query->whereIn('type_id', $type);
        }

        if ($month = self::setFilterField($data, 'month')) {
            $query->where('title', 'like', "%$month%");
        }

//        if ($order = self::setFilterField($data, 'order')) {
//            $order = self::getModelClass()::AUDIT_ORDERS[$order];
//            $query->where('title', 'like', "%$order%");
//        }

        if ($creator = self::setFilterField($data, 'creator')) {
            $creator = User::find($creator);
            $reportsOfCreator = ActivityLogRepo::filter(subject: Report::class, causer: $creator)
                ->pluck('subject_id')
                ->toArray();
            $query->whereIn('id', $reportsOfCreator);
        }

        return $query;
    }

    private static function setFilterField(array $data, string $field)
    {
        return (isset($data[$field]) && $data[$field] != 'all') ? $data[$field] : null;
    }

    public static function create(array $data)
    {
        try {
            return Report::create([
                'status_id' => Arr::get($data, 'status_id'),
                'type_id' => Arr::get($data, 'report_type'),
                'title' => Arr::get($data, 'report_title'),
                'gateway_id' => Arr::get($data, 'gateway'),
                'from' => Arr::get($data, 'from'),
                'to' => Arr::get($data, 'to'),
                'created_at' => now(),
            ]);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
