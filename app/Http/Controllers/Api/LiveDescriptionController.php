<?php

namespace App\Http\Controllers\Api;

use App\Classes\LiveDescriptionPolicy;
use App\Classes\Search\SearchStrategy\AlaaSearch;
use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\LiveDescriptionRequest;
use App\Http\Resources\LiveDescriptionResource;
use App\Models\LiveDescription;
use App\Models\Product;
use App\Repositories\LiveDescriptionRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class LiveDescriptionController extends Controller
{
    public function __construct()
    {
        $this->callMiddleware();
    }

    private function callMiddleware()
    {
        $this->middleware('permission:'.config('constants.INSERT_LIVE_DESCRIPTION_ACCESS'), ['only' => ['store'],]);
        $this->middleware('permission:'.config('constants.UPDATE_LIVE_DESCRIPTION_ACCESS'), ['only' => ['update'],]);
        $this->middleware('permission:'.config('constants.DELETE_LIVE_DESCRIPTION_ACCESS'), ['only' => 'destroy']);
        $this->middleware('permission:'.config('constants.PIN_LIVE_DESCRIPTION_ACCESS'), ['only' => 'pin']);
        $this->middleware('permission:'.config('constants.UNPIN_LIVE_DESCRIPTION_ACCESS'), ['only' => 'unpin']);
    }

    public function index(Request $request, AlaaSearch $searchClass)
    {
        $owner = config('constants.ALAA_OWNER');
        if ($request->has('owner')) {
            $owner = $request->get('owner');
        }

        LiveDescriptionPolicy::check(auth('api')->user(), $owner);
        $request->offsetSet('owner', $owner);

        $isPro = $request->get('isPro', 0);
        if (!$request->has('entity_id')) {
            if ($isPro) {
                $request->offsetSet('entity_ids', array_merge(array_keys(Product::ALL_ABRISHAM_PRO_PRODUCTS), [9]));
            } else {
                $request->offsetSet('entity_ids', array_merge(array_keys(Product::ALL_ABRISHAM_PRODUCTS), [5]));
            }
        }
        $inputs = $request->all();
        if ($request->has('length') && $request->length > 0) {
            $liveDescriptions = $searchClass->searchLiveDescriptions($inputs, $request->length);
        } else {
            $liveDescriptions = $searchClass->searchLiveDescriptions($inputs);
        }
        return LiveDescriptionResource::collection($liveDescriptions);
    }

    public function show(LiveDescription $liveDescription)
    {
        LiveDescriptionPolicy::check(auth('api')->user(), $liveDescription->owner);
        return new LiveDescriptionResource($liveDescription);
    }

    public function store(LiveDescriptionRequest $request)
    {
        LiveDescriptionPolicy::check(auth('api')->user(), $request->owner);
        try {
            if ($request->has('photo')) {
                $uploadedFileName = $this->uploadFile($request->file('photo'));
            }

            $params = $request->validated();
            $params['photo'] = $uploadedFileName ?? null;

            $liveDescription = LiveDescription::create($params);
        } catch (Exception $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'عملیات با خطا مواجه شد!');
        }

        return new LiveDescriptionResource($liveDescription);
    }

    private function uploadFile($file): string
    {
        return Uploader::put($file, config('disks.LIVE_DESCRIPTION_MINIO'));
    }

    public function destroy(LiveDescription $liveDescription)
    {
        LiveDescriptionPolicy::check(auth('api')->user(), $liveDescription->owner);
        $liveDescription->delete();
        Cache::tags(['live_description', 'live_description_search', 'search'])->flush();
        return \response()->json(['Ok']);
    }

    public function getPined(Request $request)
    {
        $pinnedLiveDescriptions = Cache::tags(['pinned_live_descriptions'])
            ->remember('pinned_live_descriptions', config('constants.CACHE_600'), function () {
                return LiveDescription::where('pinned_at', '<>', null)->orderBy('pinned_at', 'DESC')->latest()
                    ->paginate(20);
            });
        return LiveDescriptionResource::collection($pinnedLiveDescriptions);
    }

    public function unpin(Request $request, LiveDescription $liveDescription)
    {
        LiveDescriptionPolicy::check(auth('api')->user(), $liveDescription->owner);
        $liveDescription->update(['pinned_at' => null]);
        return new LiveDescriptionResource($liveDescription);
    }

    public function update(LiveDescriptionRequest $request, LiveDescription $liveDescription)
    {
        LiveDescriptionPolicy::check(auth('api')->user(), $liveDescription->owner);
        try {
            if ($request->has('photo')) {
                $uploadedFileName = $this->uploadFile($request->file('photo'));
            }

            $params = $request->validated();
            $params['photo'] = $uploadedFileName ?? null;

            $liveDescription->update($params);
            Cache::tags(['live_description', 'live_description_search', 'search'])->flush();
        } catch (Exception $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'عملیات با خطا مواجه شد!');
        }

        return new LiveDescriptionResource($liveDescription);
    }

    public function pin(Request $request, LiveDescription $liveDescription)
    {
        LiveDescriptionPolicy::check(auth('api')->user(), $liveDescription->owner);
        $liveDescription->update(['pinned_at' => now()->toDateTimeString()]);
        return new LiveDescriptionResource($liveDescription);
    }

    public function increaseSeen(Request $request, LiveDescription $liveDescription)
    {
        LiveDescriptionRepo::increaseSeenCounter($liveDescription);
        return new LiveDescriptionResource($liveDescription);
    }
}
