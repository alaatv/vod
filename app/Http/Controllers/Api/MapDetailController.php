<?php

namespace App\Http\Controllers\Api;

use App;
use App\Classes\Search\MapDetailSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditMapDetailRequest;
use App\Http\Requests\InsertMapDetailRequest;
use App\Http\Requests\MapDetailRequest;
use App\Http\Resources\MapDetail as MapDetailResource;
use App\Models\MapDetail;
use App\Traits\FileCommon;
use App\Traits\RequestCommon;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class MapDetailController extends Controller
{
    use FileCommon;
    use RequestCommon;

    /**
     * MapDetailController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.INSERT_MAP_DETAIL'),
            ['only' => 'store', 'update', 'destroy']);
    }


    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @param  MapDetailSearch  $mapDetailSearch
     *
     * @return JsonResponse
     */
    public function index(MapDetailRequest $request, MapDetailSearch $mapDetailSearch)
    {
        if ($request->has('length') && $request->get('length') > 0) {
            $mapDetailSearch->setNumberOfItemInEachPage($request->get('length'));
        }

        $mapDetails = $mapDetailSearch->get($request->all());
        return MapDetailResource::collection($mapDetails)->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertMapDetailRequest  $request
     *
     * @return JsonResponse
     */
    public function store(InsertMapDetailRequest $request)
    {
        $this->refineRequest($request);
        $inputs = $request->all();
        unset($inputs['data']['latlng']);
        unset($inputs['data']['latlngs']);
        DB::beginTransaction();
        try {
            $mapDetail = MapDetail::create($inputs);
            match ($request->input('type_id')) {
                1 => $this->updateOrCreateMarkerMapDetailLatLngs($request, $mapDetail),
                2 => $this->updateOrCreatePolylineMapDetailLatLngs($request, $mapDetail),
                default => fn() => null,
            };
            DB::commit();
            return (new MapDetailResource($mapDetail))->response();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    private function refineRequest(FormRequest $request, MapDetail $mapDetail = null)
    {
        if (!$request->has('enable')) {
            $request->offsetSet('enable', 1);
        }

        if ($request->has('tags')) {
            $request->offsetSet('tags', convertTagStringToArray($request->get('tags')));
        }

        if ($request->has('data')) {
            if (isset($mapDetail)) {
                $defaultIconPath = optional(optional(optional(optional(json_decode($mapDetail->getRawOriginal('data')))->icon))->options)->iconUrl;
            }

            $data = $request->get('data');

            $photoCopyAddress = $request->get('photo_address');
            if (isset($icon)) {
                $data->icon->options->iconUrl = $icon;
            } elseif (isset($photoCopyAddress) && strlen($photoCopyAddress) > 0) {

                if (substr_count($photoCopyAddress,
                    config('constants.DOWNLOAD_SERVER_PROTOCOL').config('constants.CDN_SERVER_NAME'))) {
                    $iconSplit = explode(config('constants.DOWNLOAD_SERVER_PROTOCOL').config('constants.CDN_SERVER_NAME').'/',
                        $photoCopyAddress);
                    $data->icon->options->iconUrl = Arr::get($iconSplit, 1);
                } else {
                    $data->icon->options->iconUrl = $photoCopyAddress;
                }

            } elseif (isset($defaultIconPath)) {
                $data->icon->options->iconUrl = $defaultIconPath;
            } elseif (isset($data->icon)) {
                $data->icon->options->iconUrl = null;
            }

            $request->offsetSet('
            ', json_encode($data));
        }


        $entityId = $request->get('entity_id');
        $entityIdCondition = (isset($entityId) && strlen($entityId) > 0 && $$entityId !== 'null');

        $entityType = $request->get('entity_type');
        $entityTypeCondition = (isset($entityType) && strlen($entityType) > 0 && $$entityType !== 'null');

        if (!($request->has('action') && $entityIdCondition && $entityTypeCondition)) {
            return;
        }
        $entityId = $request->get('entity_id');
        $entityType = ucfirst($request->get('entity_type'));
        $action = $request->get('action');
        $action = json_decode($action);

        $actionName = $action->name;
        if ($actionName == 'link') {
            $entityClassName = App::class.$entityType;
            if (class_exists($entityClassName)) {
                $entity = $entityClassName::find($entityId);
                if (isset($entity)) {
                    $action->data->link = optional($entity)->url;
                    $request->offsetSet('action', json_encode($action));
                } else {
                    $request->offsetUnset('entity_id');
                    $request->offsetUnset('entity_type');
                }
            }
        }
    }

    private function updateOrCreateMarkerMapDetailLatLngs($request, $mapDetail)
    {
        $latlngs = $request->input('data')['latlng'];
        $mapDetail->latlngs()->updateOrCreate(
            ['map_detail_id' => $mapDetail->id],
            [
                'lat' => $latlngs['lat'],
                'lng' => $latlngs['lng'],
            ]
        );
    }

    private function updateOrCreatePolylineMapDetailLatLngs($request, $mapDetail)
    {
        $latlngs = $request->input('data')['latlngs'];
        $data = [];
        foreach ($latlngs as $latlng) {
            $data[] = [
                'map_detail_id' => $mapDetail->id,
                'lat' => $latlng['lat'],
                'lng' => $latlng['lng'],
            ];
        }
        $mapDetail->latlngs()->delete();
        $mapDetail->latlngs()->createMany($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  MapDetail  $mapDetail
     *
     * @return JsonResponse
     */
    public function show(MapDetail $mapDetail)
    {
        return (new MapDetailResource($mapDetail))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditMapDetailRequest  $request
     * @param  MapDetail  $mapDetail
     *
     * @return JsonResponse
     */
    public function update(EditMapDetailRequest $request, MapDetail $mapDetail)
    {
        $this->refineRequest($request, $mapDetail);
        $inputs = $request->all();
        unset($inputs['data']['latlng']);
        unset($inputs['data']['latlngs']);
        DB::beginTransaction();
        try {
            $mapDetail->update($inputs);
            match ($request->input('type_id')) {
                1 => $this->updateOrCreateMarkerMapDetailLatLngs($request, $mapDetail),
                2 => $this->updateOrCreatePolylineMapDetailLatLngs($request, $mapDetail),
                default => fn() => null,
            };
            DB::commit();
            return (new MapDetailResource($mapDetail->fresh()))->response();
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  MapDetail  $mapDetail
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(MapDetail $mapDetail)
    {
        $result = $mapDetail->delete();

        if (!$result) {
            return response()->json(['message' => 'خطای پایگاه داده'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new MapDetailResource($mapDetail))->response();
    }
}
