<?php

namespace App\Http\Controllers\Api;

use App\Events\Plan\PlanSyncContentsEvent;
use App\Events\Plan\StudyPlanSyncContentsEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditPlanRequest;
use App\Http\Requests\InsertPlanRequest;
use App\Http\Resources\Plan as PlanResource;
use App\Http\Resources\Study;
use App\Http\Resources\StudyPlan2 as StudyPlanResource;
use App\Models\Plan;
use App\Models\Studyplan;
use App\Repositories\PlanRepo;
use App\Repositories\StudyplanRepo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PlanController extends Controller
{
    /**
     * PlanController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
        $this->middleware('permission:'.config('constants.INSERT_PLAN'), ['only' => 'store']);
        $this->middleware('permission:'.config('constants.UPDATE_PLAN'), ['only' => 'update']);
        $this->middleware('permission:'.config('constants.DELETE_PLAN'), ['only' => 'delete']);
    }

    public function index(Request $request)
    {
        $plans = PlanRepo::getAllPlanOrderByDate(['studyplan_id' => $request->get('studyPlan_id')])->get();

        return PlanResource::collection($plans);
    }

    public function show(Plan $plan)
    {
        return new PlanResource($plan);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertPlanRequest  $request
     *
     * @return
     */
    public function store(InsertPlanRequest $request)
    {
        /** @var Studyplan $studyPlan */
        $studyPlan = StudyplanRepo::findByDateOrCreate($request->get('date'), $request->get('event_id'));

        if (!isset($studyPlan)) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error on creating study plan');
        }

        /** @var Plan $plan */
        $plan = Plan::query()->create(array_merge(
            ['studyplan_id' => $studyPlan->id]
            , $request->validated()
        ));

        if (!isset($plan)) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error on creating plan');
        }

        if ($request->has('contents')) {
            event(new PlanSyncContentsEvent($plan, $request->get('contents', [])));
            event(new StudyPlanSyncContentsEvent($studyPlan, $request->get('contents', [])));
        }

        $studyPlans = StudyplanRepo::findByEventId($request->get('event_id'))->get();
        $plans = PlanRepo::getAllPlanOrderByDate(['studyplan_id' => $studyPlans->pluck('id')->toArray()])->get();

        Cache::tags(['showStudyEvent'])->flush();

        return (new Study([
            'days' => StudyPlanResource::collection($studyPlans),
            'events' => PlanResource::collection($plans),
        ]))->response();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditPlanRequest  $request
     * @param  Plan  $plan
     *
     * @return JsonResponse
     */
    public function update(EditPlanRequest $request, Plan $plan)
    {

        $studyEventId = optional($plan->studyplan)->event_id;
        $studyPlan = StudyplanRepo::findByDateOrCreate($request->get('date'), $studyEventId);

        if (!isset($studyPlan)) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error on creating study plan');
        }

        $request->offsetSet('studyplan_id', $studyPlan->id);

        $updateResult = $plan->update(array_merge(
            ['studyplan_id' => $studyPlan->id]
            , $request->validated()
        ));

        if (!$updateResult) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error on updating plan');
        }

        if ($request->has('contents')) {
            event(new PlanSyncContentsEvent($plan, $request->get('contents', [])));
            event(new StudyPlanSyncContentsEvent($studyPlan, $request->get('contents', [])));
        }

        $studyPlans = StudyplanRepo::findByEventId($studyEventId)->get();
        $plans = PlanRepo::getAllPlanOrderByDate(['studyplan_id' => $studyPlans->pluck('id')->toArray()])->get();

        Cache::tags(['showStudyEvent'])->flush();

        return (new Study([
            'days' => StudyPlanResource::collection($studyPlans),
            'events' => PlanResource::collection($plans),
        ]))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Plan  $plan
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Plan $plan)
    {
        if (!$plan->delete()) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error on deleting plan');
        }

        $studyEventId = optional($plan->studyplan)->event_id;
        $studyPlans = StudyplanRepo::findByEventId($studyEventId, $studyEventId)->get();
        $plans = PlanRepo::getAllPlanOrderByDate(['studyplan_id' => $studyPlans->pluck('id')->toArray()])->get();

        Cache::tags(['showStudyEvent'])->flush();

        return (new Study([
            'days' => StudyPlanResource::collection($studyPlans),
            'events' => PlanResource::collection($plans),
        ]))->response();
    }
}
