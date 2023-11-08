<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\ContentsetSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttachProductsToSetRequest;
use App\Http\Resources\ContentOfSet;
use App\Http\Resources\SetInIndex;
use App\Http\Resources\SetWithContents;
use App\Http\Resources\SetWithoutPaginationV2;
use App\Jobs\DeleteOrTransferContentToDanaProductJob;
use App\Models\Content;
use App\Models\Contentset;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaSetTransfer;
use App\Models\Product;
use App\Repositories\ContentRepository;
use App\Services\ContentSetService;
use App\Services\DanaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SetController extends Controller
{
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

    public function indexContentLinks(Request $request, Contentset $set)
    {
        $contents = optional($set->contents)->where('contenttype_id', Content::CONTENT_TYPE_VIDEO)->sortBy('order');
        $links = $contents->map(function ($contentVideo) {
            return $contentVideo->url;
        })->all();
        return response()->json($links, 200);
    }

    public function indexContent(Request $request, Contentset $set)
    {
        $sumVideoContentDuration = secondsToHumanFormat($this->contentSetVideoContentsStatistics($set)['total_seconds']);
        $contents = optional($set->contents)->sortBy('order');

        $responseData = [
            'set' => $set,
            'contents' => $contents,
            'sumVideoContentDuration' => $sumVideoContentDuration
        ];

        return response()->json($responseData, 200);
    }

    public function transferToDana(Request $request, Contentset $set)
    {
        if (!$set->isActive()) {
            session()->flash('error', 'نمی توانید ست غیرفعال را منتقل کنید');
            return redirect()->back();
        }
        if (!is_null($set->redirectUrl)) {
            session()->flash('error', 'نمی توانید ست ریدایرکت شده را منتقل کنید');
            return redirect()->back();
        }
        $setProductIds = $set->products->pluck('id');
        $foriatIds = array_merge(Product::ALL_FORIYAT_110_PRODUCTS, [Product::ARASH_TETA_SHIMI, Product::TETA_ADABIAT]);
        $productIntersect = $setProductIds->intersect($foriatIds)->all();

        if (!empty($productIntersect)) {
            return $this->transferToDanaTypeOne($set, $request->get('renew', false));
        } else {
            return $this->transferToDanaTypeTwo($set, $request->get('renew', false));
        }
    }

    public function toggleProductForSet(
        AttachProductsToSetRequest $request,
        Contentset $contentSet,
        ContentSetService $contentSetService
    ): JsonResponse {
        $contentSetService->toggleProduct($request->input('product_id'), $contentSet, $request->input('order') ?? 0);
        $responseData = ['message' => 'عملیات با موفقیت انجام شد'];
        return response()->json($responseData, 200);
    }

    /**
     * @param $setId
     * @return JsonResponse
     */
    public function TransferToDanaInfo($setId): JsonResponse
    {
        $danaSets = DanaProductSetTransfer::where('contentset_id', $setId)->get();
        $insertType = 2;
        if ($danaSets->isEmpty()) {
            $danaSets = DanaSetTransfer::where('contentset_id', $setId)->get();
            $insertType = 1;
        }
        if ($danaSets->isEmpty()) {
            return response()->json(['error' => 'Data not found'], 404);
        }
        return response()->json(['danaSets' => $danaSets, 'insertType' => $insertType, 'setId' => $setId], 200);
    }

    private function transferToDanaTypeOne($set, $renew)
    {
        $danaSet = DanaSetTransfer::where('contentset_id', $set->id)->get();
        if ($danaSet->isEmpty()) {
            $courseKey = DanaService::createCourse($set, $set->products->first());
            if ($courseKey == false) {
                return response()->json(['error' => 'مشکلی در ساخت دوره پیش آمده است'], 500);
            }
        }
        return response()->json(['message' => 'دوره با موفقیت ساخته شد'], 200);
    }

    private function transferToDanaTypeTwo($set, $renew)
    {
        $contents = $set->activeContents()->whereNull('redirectUrl')->get();

        if ($contents->isEmpty()) {
            return response()->json(['error' => 'این ست کانتنتی برای انتقال ندارد'], 404);
        }

        foreach ($contents as $content) {
            DeleteOrTransferContentToDanaProductJob::dispatch($content, $renew);
        }

        return response()->json(['success' => 'کانتنت های این ست در صف انتقال قرار گرفتند'], 200);
    }

    public function bulkActivate(SetBulkActivateRequest $request)
    {
        $sets = Contentset::with('contents')->whereIn('id', $request->input('set_ids'))->get();
        $contentIds = [];
        $setIds = [];
        foreach ($sets as $set) {
            foreach ($set->contents as $content) {
                if ($content->urlExist()) {
                    $contentIds[] = $content->id;
                    if (!in_array($set->id, $setIds)) {
                        $setIds[] = $set->id;
                    }
                }
            }
        }
        if (!empty($contentIds)) {
            $contents = Content::whereIn('id', $contentIds)->get();
            foreach ($contents as $content) {
                ContentRepository::update($content, ['enable' => 1]);
            }
            Contentset::whereIn('id', $setIds)->update([
                'enable' => 1,
            ]);
            foreach ($setIds as $setId) {
                Cache::tags([
                    'contentset_'.$setId, 'set_search',
                    'set_'.$setId.'_contents',
                    'set_'.$setId.'_setMates',
                ])->flush();
                $products = Product::whereHas('sets', function ($query) use ($setId) {
                    $query->where('id', $setId);
                })->get();
                foreach ($products as $product) {
                    Cache::tags('product_'.$product->id)->flush();
                }
            }
            foreach ($contentIds as $contentId) {
                Cache::tags([
                    'content_'.$contentId,
                    'content_search',
                    'set_search',
                ])->flush();
            }
            return response()->json(['success' => 'محتواها با موفقیت فعال شدند'], 200);
        }
        return response()->json(['error' => 'محتوایی برای فعال سازی یافت نشد از آپلود کردن فایل محتوا اطمینان حاصل کنید'],
            404);
    }
}
