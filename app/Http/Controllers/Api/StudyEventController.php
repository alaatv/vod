<?php

namespace App\Http\Controllers\Api;

use App\Classes\LastWatch;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttachUserStudyEventRequest;
use App\Http\Requests\EventProductRequest;
use App\Http\Resources\ContentForStudyPlanResource;
use App\Http\Resources\Major;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\SetWithoutPaginationV2;
use App\Http\Resources\StudyEventResource;
use App\Http\Resources\StudyPlan;
use App\Models\Contentset;
use App\Models\Contentset;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Studyevent;
use App\Models\Studyevent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Validator;

class StudyEventController extends Controller
{
    public function index()
    {
        return StudyEventResource::collection(Studyevent::all());
    }

    /**
     * @param  Request  $request
     * @return ResourceCollection|JsonResponse
     */
    public function whereIsKarvan(Request $request)
    {
        $date = $request->date ?? today('Asia/Tehran');
        $now = now('Asia/Tehran')->format('H:i:s');
        $studyEvent = Studyevent::find($request->get('studyevent'));
        if (!$studyEvent) {
            return myAbort(\Symfony\Component\HttpFoundation\Response::HTTP_EXPECTATION_FAILED,
                'برنامه مطالعاتی یافت نشد');
        }

        $key = 'event:abrisham1401:whereIsKarvan:'.$date;
        $cachedContentsCollection =
            Cache::tags(['content', 'event', 'plan', 'event_abrisham1401_whereIsKarvan_'.$date])
                ->remember($key, config('constants.CACHE_600'), function () use ($date, $now, $studyEvent) {

                    $eventStudyPlans = $studyEvent
                        ->studyPlans()
                        ->where('plan_date', $date)
                        ->get();

                    $contentsCollection = collect();
                    foreach ($eventStudyPlans as $studyPlan) {
                        foreach ($studyPlan->plans()->orderBy('start')->get() as $plan) {
                            /** @var Plan $plan */
                            $planMajor = $plan->major;
                            $contents = $plan->contents()
                                ->active()
                                ->get();
                            foreach ($contents as $content) {
                                $content->plan_start = $plan->start;
                                $content->plan_end = $plan->end;
                                $content->plan_is_current = $date == $plan->studyplan->plan_date && $now >= $plan->start && $now <= $plan->end;
                                $content->plan_major = $planMajor ? new Major($plan->major) : null;
                                $contentsCollection->push($content);
                            }
                        }
                    }
                    return $contentsCollection;
                });
        return ContentForStudyPlanResource::collection($cachedContentsCollection);
    }

    /**
     * @param  Request  $request
     * @param  Studyevent  $studyEvent
     * @return ResourceCollection
     */
    public function studyPlans(Request $request, Studyevent $studyEvent)
    {
        return StudyPlan::collection($studyEvent->studyPlans()->orderBy('plan_date')->get());
    }

    public function whereIsEvent(Request $request)
    {
        Validator::make($request->all(), [
            'studyevent_id' => ['required', 'integer', 'min:1'],
        ])->validate();

        $studyEventId = $request->get('studyevent_id');
        $date = $request->date ?? today('Asia/Tehran');
        $now = now('Asia/Tehran')->format('H:i:s');

        $key = 'event:'.$studyEventId.':whereIsStudyEvent:'.$date;
        $cachedContentsCollection = Cache::tags(['content', 'event', 'plan', 'event_'.$studyEventId.'_'.$date])
            ->remember($key, config('constants.CACHE_600'), function () use ($date, $now, $studyEventId) {

                $eventStudyPlans = Studyevent::find($studyEventId)
                    ->studyPlans()
                    ->where('plan_date', $date)
                    ->get();

                $contentsCollection = collect();
                foreach ($eventStudyPlans as $studyPlan) {
                    foreach ($studyPlan->plans()->orderBy('start')->get() as $plan) {
                        /** @var Plan $plan */
                        $planMajor = $plan->major;
                        $contents = $plan->contents()
                            ->active()
                            ->get();
                        foreach ($contents as $content) {
                            $content->plan_start = $plan->start;
                            $content->plan_end = $plan->end;
                            $content->plan_is_current = $date == $plan->studyplan->plan_date && $now >= $plan->start && $now <= $plan->end;
                            $content->plan_major = $planMajor ? new Major($plan->major) : null;
                            $contentsCollection->push($content);
                        }
                    }
                }
                return $contentsCollection;
            });
        return ContentForStudyPlanResource::collection($cachedContentsCollection);
    }

    public function advisor(Studyevent $studyEvent)
    {
        // ToDo  : REFACTOR --> get advisor set using relation that it's name is sets and forrest tag of advisor
        switch ($studyEvent->id) {
            case 8 :
                $moshavereSetId = Contentset::ABRISHAM_MOSHAVERE_SE_ID;
                break;
            case 10 :
                $moshavereSetId = Contentset::PARACHUTE_SET_ID;
                break;
            case 11 :
                $moshavereSetId = Contentset::NAHAYI_1402_MOSHAVERE_SET_ID;
                break;
            case 13 :
            case 14 :
            case 15 :
            case 16:
            case 17 :
            case 18 :
            case 19 :
            case 20 :
            case 21 :
            case 22 :
            case 23 :
            case 24 :
                $moshavereSetId = 3460;
                break;
            case 25 :
                $moshavereSetId = 4669;
                break;
            default :
                return new SetWithoutPaginationV2(null);
        }
        $set = Contentset::find($moshavereSetId);
        if (isset($set)) {
            $countOfSetsContents = $set->contents()
                ->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))
                ->get()
                ->count();
            $contOfUserWatched = auth()->user()->watchContents()->wherePivot('studyevent_id', $studyEvent->id)
                ->whereHas('set', function ($query) use ($set) {
                    $query->whereId($set->id);
                })->get()->count();
            $set->contents_progress =
                ($countOfSetsContents) ? (int) round(($contOfUserWatched / $countOfSetsContents) * 100) : 0;
        }
        return new SetWithoutPaginationV2($set);
    }

    public function products(EventProductRequest $request, Studyevent $studyEvent)
    {
        switch ($studyEvent->id) {
            case 10 :
                $productIds = match ($request->get('major_id')) {
                    '1' => Product::ALL_CHATR_NEJAT2_PRODUCTS_EKHTESASI_RIYAZI,
                    '2' => Product::ALL_CHATR_NEJAT2_PRODUCTS_EKHTESASI_TAJROBI,
                    '3' => Product::ALL_CHATR_NEJAT2_ENSANI_PRODUCTS,
                    default => null
                };
                break;
            case 11 :
                $productIds = match ($request->get('major_id')) {
                    '1' => Product::ALL_NAHAYI_1402_PRODUCTS_EKHTESASI_RIYAZI,
                    '2' => Product::ALL_NAHAYI_1402_PRODUCTS_EKHTESASI_TAJROBI,
                    '3' => [],
                    default => null
                };
                break;
            case 12 :
                $productIds = match ($request->get('major_id')) {
                    '1' => Product::ALL_EMTEHAN_NAHAYI_NOHOM_1402,
                    '2' => [],
                    '3' => [],
                    default => null
                };
                break;
            case 13 :
            case 14 :
            case 15 :
            case 16:
            case 17 :
            case 18 :
            case 19 :
            case 20 :
            case 21 :
            case 22 :
            case 23 :
            case 24 :
                $productIds = match ($request->get('major_id')) {
                    '1' => [1101, 1099, 1095, 1094, 1091, 1090],
                    '2' => [1100, 1095, 1094, 1093, 1092],
                    '3' => [1098,],
                    default => null
                };
                break;
            case 25 :
                $productIds = match ($request->get('major_id')) {
                    '1' => [781, 782, 784, 983],
                    '2' => [785, 787, 788, 789, 983],
                    '3' => [790, 791, 792, 796, 797, 798, 800, 951, 1098],
                    default => null
                };
                break;
            default :
                return myAbort(Response::HTTP_BAD_REQUEST, 'محصولی برای رشته انتخاب شده یافت نشد');
        }

        $productsWithLastWatch = new LastWatch($request->user(), 'product', $productIds, $studyEvent->id);
        $productsWithLastWatch = $productsWithLastWatch->get();
        return ProductResource::collection($productsWithLastWatch);
    }

    public function showMyStudyEvent()
    {
        $userActiveStudyEvent = auth()->user()->getActiveStudyEvents()->first();
        if (!isset($userActiveStudyEvent)) {
            return myAbort(Response::HTTP_NO_CONTENT, 'شما هنوز برنامه مطالعاتی انتخاب نکرده اید');
        }
        $studyEventStartDate = Carbon::parse($userActiveStudyEvent->start_at);
        $allEventSessions = 0;
        $studyPlans = $userActiveStudyEvent->studyplans()->with([
            'plans' => function ($query) {
                $query->with([
                    'contents' => function ($query) {
                        $query->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'));
                    },
                ]);
            },
        ])->get();
        $studyPlans->each(function ($studyPlan) use (&$allEventSessions) {
            $studyPlan->plans->each(function ($plan) use (&$allEventSessions) {
                $allEventSessions += $plan->contents->count();
            });
        });
        return response()->json([
            'data' => [
                'id' => $userActiveStudyEvent->id,
                'title' => $userActiveStudyEvent->studyEventMethod->display_name,
                'passed_days' => $studyEventStartDate->diffInDays(now()),
                'count_of_watched_sessions' => $userActiveStudyEvent->watchHistories->count(),
                'count_of_remained_sessions' => $allEventSessions,
            ],
        ]);
    }

    public function storeMyStudyEvent(AttachUserStudyEventRequest $request)
    {
        $user = $request->user();
        $inputs = $request->validated();
//        $user->major_id = $inputs['major_id'];
//        $user->grade_id = $inputs['grade_id'];
//        $user->save();
        $userActiveStudyEvent = $user->getActiveStudyEvents()->first();
        $studyEvent =
            Studyevent::findByMethodAndMajorAndGrade($inputs['study_method_id'], $inputs['major_id'],
                $inputs['grade_id'])->first();
        if (!isset($studyEvent)) {
            return myAbort(Response::HTTP_FORBIDDEN, 'برنامه انتخاب یافت نشد');
        }
        if (isset($userActiveStudyEvent) && $userActiveStudyEvent->id === $studyEvent->id) {
            return myAbort(Response::HTTP_FORBIDDEN, 'شما در حال حاضر با این برنامه پیش می روید');
        }
        if (!$user->hasRole(config('constants.STUDY_PLAN_EMPLOYEE')) && $user->studyEvents->count() > Studyevent::ABRISHAM_2_CHANGE_LIMIT) {
            return myAbort(Response::HTTP_FORBIDDEN, 'تعداد مجاز تغییر برنامه تمام شده است');
        }
        $user->studyEvents()->attach($studyEvent->id);
        $userActiveStudyEvent?->watchHistories()->delete();
        return response()->json([
            'data' => [
                'id' => $studyEvent->id,
                'title' => $studyEvent->studyEventMethod->display_name,
            ],
        ]);
    }

    public function findStudyPlan(AttachUserStudyEventRequest $request)
    {
        $inputs = $request->validated();
        $studyEvent =
            Studyevent::findByMethodAndMajorAndGrade($inputs['study_method_id'], $inputs['major_id'],
                $inputs['grade_id']);
        if (!isset($studyEvent)) {
            return myAbort(Response::HTTP_FORBIDDEN, 'برنامه انتخاب یافت نشد');
        }
        return response()->json([
            'data' => [
                'id' => $studyEvent?->id,
                'title' => $studyEvent?->studyEventMethod?->display_name,
            ],
        ]);
    }
}
