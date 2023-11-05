<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\City;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $cities = City::orderBy('name');

        $cityIds = $request->get('ids');
        if (is_string($cityIds)) {
            $cityIds = json_decode($cityIds);
        }
        if (isset($cityIds)) {
            $cities = $cities->whereIn('id', $cityIds);
        }

        $provinceIds = $request->get('provinces');
        if (is_string($provinceIds)) {
            $provinceIds = json_decode($provinceIds);
        }
        if (isset($provinceIds)) {
            $cities = $cities->whereIn('province_id', $provinceIds);
        }

        return $cities->get();
    }
}