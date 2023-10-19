<?php

namespace App\Observers;

use App\Models\Plan;
use Illuminate\Support\Facades\Cache;

class PlanObserver
{
    /**
     * Handle the Orderfile "created" event.
     *
     * @param  Plan  $plan
     * @return void
     */
    public function created(Plan $plan)
    {
        //
    }

    /**
     * Handle the Plan "updated" event.
     *
     * @param  Plan  $plan
     * @return void
     */
    public function updated(Plan $plan)
    {
        //
    }

    /**
     * Handle the Plan "deleted" event.
     *
     * @param  Plan  $plan
     * @return void
     */
    public function deleted(Plan $plan)
    {
        Cache::tags(['event_abrisham1401_whereIsKarvan_'.optional($plan->studyplan)->plan_date])->flush();
    }

    /**
     * Handle the Plan "restored" event.
     *
     * @param  Plan  $plan
     * @return void
     */
    public function restored(Plan $plan)
    {
        //
    }

    /**
     * Handle the Plan "force deleted" event.
     *
     * @param  Plan  $plan
     * @return void
     */
    public function forceDeleted(Plan $plan)
    {
        //
    }

    /**
     * Handle the Plan "creating" and "updating" event.
     *
     * @param  Plan  $plan
     */
    public function saving(Plan $plan)
    {
        //
    }

    /**
     * Handle the Plan "created" and "updated" event.
     *
     * @param  Plan  $plan
     */
    public function saved(Plan $plan)
    {
        Cache::tags(['event_abrisham1401_whereIsKarvan_'.optional($plan->studyplan)->plan_date])->flush();
    }
}
