<?php

namespace App\Http\Controllers\Api;

use App\Events\Plan\StudyPlanSyncContentsEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudyPlanFilterRequest;
use App\Http\Requests\UpdateStudyPlanRequest;
use App\Http\Resources\Admin\StudyPlan as StudyPlanResource;
use App\Http\Resources\Plan as PlanResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Studyevent;
use App\Models\Studyplan;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class StudyPlanController extends Controller
{
    /**
     * PlanController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.INSERT_STUDY_PLAN'), ['only' => 'store']);
        $this->middleware('permission:'.config('constants.UPDATE_STUDY_PLAN'), ['only' => 'update']);
        $this->middleware('permission:'.config('constants.DELETE_STUDY_PLAN'), ['only' => 'delete']);
    }

    public function index(StudyPlanFilterRequest $request)
    {
        $since_date = $request->query('since_date');
        $till_date = $request->query('till_date');
        $studyEventId = $request->query('study_event');
        $productId = $request->query('product_id');
        $studyPlans = Studyplan::with([
            'plans' => function ($query) {
                $query->with('contents');
            }
        ])
            ->when($request->has('study_event'), function ($query) use ($studyEventId) {
                $query->where('event_id', $studyEventId);
            })
            ->when($request->has('since_date'), function ($query) use ($since_date) {
                $query->where('plan_date', '>=', $since_date);
            })
            ->when($request->has('till_date'), function ($query) use ($till_date) {
                $query->where('plan_date', '<=', $till_date);
            })
            ->when($request->has('product_id'), function ($query) use ($productId) {
                $query->whereHas('plans', function ($query) use ($productId) {
                    $query->whereHas('contents', function ($query) use ($productId) {
                        $query->whereHas('set', function ($query) use ($productId) {
                            $query->whereHas('products', function ($query) use ($productId) {
                                $query->whereId($productId);
                            });
                        });
                    });
                });
            })
            ->orderBy('plan_date')->get();
        return StudyPlanResource::collection($studyPlans);
    }

    /**
     * @param  Request  $request
     * @param  string  $planDate
     * @param  Studyevent  $event
     *
     * @return JsonResponse
     */
    public function updateByDateAndEvent(Request $request, string $planDate, Studyevent $event)
    {
        $studyPlan = Studyplan::query()->where('plan_date', $planDate)
            ->where('event_id', $event->id)
            ->first();

        if (!isset($studyPlan)) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        $this->fillAttributeFromRequest($request->all(), $studyPlan);

        try {
            $studyPlan->update();
        } catch (Exception $e) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'خطای پایگاه داده');
        }

        Cache::tags(['showStudyEvent'])->flush();

        return (new StudyPlanResource($studyPlan))->response();
    }

    /**
     * Fill the model object to be stored or updated in database.
     *
     * @param  array  $inputData
     * @param  Studyplan  $studyPlan
     */
    private function fillAttributeFromRequest(array $inputData, Studyplan $studyPlan): void
    {
        if (array_has($inputData, 'contents')) {
            event(new StudyPlanSyncContentsEvent($studyPlan, Arr::get($inputData, 'contents', [])));
        }

        $studyPlan->fill($inputData);
    }

    /**
     * @param  UpdateStudyPlanRequest  $request
     * @param  Studyplan  $studyPlan
     * @return JsonResponse
     */
    public function update(UpdateStudyPlanRequest $request, Studyplan $studyPlan)
    {
        $this->fillAttributeFromRequest($request->all(), $studyPlan);

        try {
            $studyPlan->update();
        } catch (Exception $e) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'خطای پایگاه داده');
        }

        Cache::tags(['showStudyEvent'])->flush();

        return (new StudyPlanResource($studyPlan))->response();
    }

    /**
     * @param  Studyplan  $studyPlan
     * @return JsonResponse
     */
    public function show(Studyplan $studyPlan)
    {
        return (new StudyPlanResource($studyPlan))->response();
    }

    public function showByDateAndEvent(string $planDate, Studyevent $event)
    {
        $studyPlan = Studyplan::query()->where('plan_date', $planDate)
            ->where('event_id', $event->id)
            ->first();

        if (!isset($studyPlan)) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        return (new StudyPlanResource($studyPlan))->response();
    }

    /**
     * @param  Request  $request
     * @param  Studyplan  $studyPlan
     *
     * @return ResourceCollection
     */
    public function plans(Request $request, Studyplan $studyPlan)
    {
        return PlanResource::collection($studyPlan->plans()->orderBy('start')->get());
    }
}
