<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\ContentSearch;
use App\Classes\Search\ContentsetSearch;
use App\Classes\Search\ProductSearch;
use App\Classes\Search\SearchStrategy\SearchFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContentIndexRequest;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /**
     * @param  ContentIndexRequest  $request
     *
     * @param  ContentSearch  $contentSearch
     * @param  ContentsetSearch  $setSearch
     * @param  ProductSearch  $productSearch
     *
     * @return JsonResponse
     */
    public function index(
        ContentIndexRequest $request,
        ContentSearch $contentSearch,
        ContentsetSearch $setSearch,
        ProductSearch $productSearch
    ) {
        $searchClass = SearchFactory::factory($request->get('q', ''));
        $response = $searchClass->search($request->all());
        return response()->json($response);
    }
}
