<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\ContentsetSearch;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContentOfSet;
use App\Http\Resources\SetInIndex;
use App\Http\Resources\SetWithContents;
use App\Http\Resources\SetWithoutPaginationV2;
use App\Models\Contentset;
use App\Models\Contentset;
use Illuminate\Http\Request;

class SetController extends Controller
{
    public function show(Request $request, Contentset $set)
    {
        if (isset($set->redirectUrl)) {
            $redirectUrl = $set->redirectUrl;
            return redirect(convertRedirectUrlToApiVersion($redirectUrl['url']),
                $redirectUrl['code'], $request->headers->all());
        }

        return response()->json($set);
    }

    public function showV2(Request $request, Contentset $set)
    {
        if (!isset($set->redirectUrl)) {
            return new SetWithoutPaginationV2($set);
        }
        $redirectUrl = $set->redirectUrl;
        return redirect(convertRedirectUrlToApiVersion($redirectUrl['url'], '2'),
            $redirectUrl['code'], $request->headers->all());
    }


    public function showWithContents(Request $request, Contentset $set)
    {
        return new SetWithContents($set);
    }

    public function index(Request $request, ContentsetSearch $contentSearch)
    {
        $setFilters = $request->all();
        $setFilters['enable'] = 1;
        $setFilters['display'] = 1;
        $setResult = $contentSearch->get($setFilters);

        return SetInIndex::collection($setResult);
    }

    /**
     * @param  Request  $request
     * @param  Contentset  $set
     */
    public function contents(Request $request, Contentset $set)
    {
        return ContentOfSet::collection($set->getActiveContents2());
    }
}
