<?php

namespace App\Repositories;

use App\Models\Studyplan;
use Illuminate\Database\Eloquent\Builder;

class StudyplanRepo
{
    public static function findByDateOrCreate(string $date, int $eventId): ?Studyplan
    {
        $studyPlan = Studyplan::query()->where('plan_date', $date)->where('event_id', $eventId)->get()->first();
        if (!isset($studyPlan)) {
            $studyPlan = Studyplan::create([
                'plan_date' => $date,
                'event_id' => $eventId
            ]);
        }

        return $studyPlan;
    }

    public static function findByEventId(int $event_id, string $fromDate = null): Builder
    {
        $studyPlans = Studyplan::query()->where('event_id', $event_id);
        if (isset($fromDate)) {
            $studyPlans->where('plan_date', '>=', $fromDate);
        }
        return $studyPlans->orderBy('plan_date');
    }
}
