<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MapDetail as MapDetailResource;
use App\Models\Map;
use Illuminate\Http\Request;

class MapPageController extends Controller
{

    public function __invoke(Request $request)
    {
        $user = $request->user();
        /** @var Map $map */
        $map = Map::query()->find(2); //ToDo : After refactoring the layout this should be removed because this value will have to be received from the request

        if (!isset($map)) {
            return response()->json(['error' => 'No map found'], 404);
        }

        $mapDetails = $map->mapDetails;

        $mapDetails = (MapDetailResource::collection($mapDetails))->resource;

        $canEditMap = false;
        if (isset($user)) {
            $canEditMap = $user->isAbleTo(config('constants.INSERT_MAP_DETAIL'));
        }

        return response()->json(compact('mapDetails', 'canEditMap'), 200);
    }
}