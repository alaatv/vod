<?php

namespace App\Repositories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Builder;

class PlanRepo
{
    public static function getAllPlanOrderByDate(array $filters = []): Builder
    {
        $plans = Plan::query();
        self::filter($filters, $plans);
        return $plans;
    }

    /**
     * @param  array  $filters
     * @param       $plans
     */
    private static function filter(array $filters, Builder $plans): void
    {
        foreach ($filters as $key => $filter) {
            if (is_array($filter)) {
                $plans->WhereIn($key, $filter);
            } else {
                $plans->where($key, $filter);
            }
        }
    }

    public static function getPlansByStudyplan(array $studyplanId)
    {
        return Plan::query()->whereIn('studyplan_id', $studyplanId);
    }
}
