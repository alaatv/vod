<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\ContentsetSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttachContentsRequest;
use App\Http\Requests\ContentSetRequest;
use App\Http\Resources\Admin\SetResource;
use App\Http\Resources\ContentInSet;
use App\Http\Resources\ResourceCollection;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\Source;
use App\Services\ContentSetService;
use App\Traits\SetCommon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetController extends Controller
{
    use SetCommon;

    /**
     * SetController constructor.
     */
    public function __construct()
    {
        //        $this->middleware('permission:'.config('constants.LIST_CONTENT_SET_ACCESS'), ['only' => ['index',],]);
        //        $this->middleware('permission:'.config('constants.SHOW_CONTENT_SET_ACCESS'), ['only' => ['show',],]);
        //        $this->middleware('permission:'.config('constants.INSERT_CONTENT_SET_ACCESS'), ['only' => ['store',],]);
        //        $this->middleware('permission:'.config('constants.INSERT_EDUCATIONAL_CONTENT_ACCESS'),
        //            ['only' => ['attachContents',],]);
        //        $this->middleware('permission:'.config('constants.LIST_CONTENTS_OF_CONTENT_SET_ACCESS'),
        //            ['only' => ['contents',],]);
    }

    /**
     * @return ResourceCollection
     */
    public function index(Request $request, ContentsetSearch $setSearch)
    {
        $filters = $request->all();
        $pageName = Contentset::INDEX_PAGE_NAME;
        $setSearch->setPageName($pageName);
        if ($request->has('length')) {
            $setSearch->setNumberOfItemInEachPage($request->get('length'));
        }

        $results = $setSearch->get($filters);

        return SetResource::collection($results);
    }

    public function show(Contentset $set)
    {
        return new SetResource($set);
    }

    public function store(ContentSetRequest $request, ContentSetService $contentSetService)
    {
        $contentSet = new Contentset();
        $contentSetService->fillContentSet($request->all(), $contentSet);
        if (! $contentSet->save()) {
            return response()->json(['error' => 'خطای پایگاه داده'], 500);
        }

        $contentSetService->syncSources($request->all(), $contentSet);

        if ($request->has('products')) {
            $products = $request->get('products');
            if ($products === null) {
                $products = [];
            }

            $contentSetService->syncProducts($products, $contentSet);
        }

        return response()->json(['message' => 'دسته با موفقیت درج شد . شماره دسته : '.$contentSet->id]);
    }

    public function attachContents(AttachContentsRequest $request, Contentset $contentSet)
    {
        Content::whereIn('id', $request->get('contents'))->update(['contentset_id' => $contentSet->id]);

        return response()->json(['message' => 'تغیرات با موفقیت صورت گرفت.']);
    }

    public function update(
        ContentSetRequest $request,
        Contentset $contentSet,
        ContentSetService $contentSetService
    ): JsonResponse {
        $contentSetService->fillContentSet($request->all(), $contentSet);
        $contentSet->save();
        $contentSetService->syncSources($request->all(), $contentSet);
        if ($request->has('products')) {
            $products = $request->get('products');
            if ($products === null) {
                $products = [];
            }

            $contentSetService->syncProducts($products, $contentSet);
        }

        return response()->json([
            'data' => [
                'id' => $contentSet->id,
                'message' => 'دسته با موفقیت ویرایش شد . شماره دسته : '.$contentSet->id,
            ],
        ]);
    }

    public function destroy(Contentset $contentset): JsonResponse
    {
        $contentset->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function contents(Contentset $set): JsonResponse
    {
        $sumVideoContentDuration = secondsToHumanFormat($this->contentSetVideoContentsStatistics($set)['total_seconds']);
        $contents = optional($set->contents)->sortBy('order');

        return response()->json([
            'data' => [
                'sumVideoContentDuration' => $sumVideoContentDuration,
                'contents' => ContentInSet::collection($contents),
            ],
        ]);
    }

    public function edit(Contentset $set)
    {
        $setProducts = $set->products()->get();
        $products = $this->makeProductCollection()->whereNotIn('id', $setProducts->pluck('id'));
        $sources = Source::all()->pluck('title', 'id')->toArray();
        $setSources = $set->sources->pluck('id')->toArray();

        $redirectCodes = config('constants.REDIRECT_HTTP_RESPONSE_TYPES');
        $redirectUrl = $set->redirectUrl;

        return response()->json('set.edit', compact('set', 'setProducts', 'products', 'sources', 'setSources',
            'redirectCodes', 'redirectUrl'));
    }
}
