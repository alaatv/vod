<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductIndex;
use App\Http\Resources\ProductInLandingWithoutPagination;
use App\Http\Resources\ProductInLandingWithoutPagination as ProductLandingResource;
use App\Http\Resources\SetInIndex;
use App\Models\Block;
use App\Models\Contentset;
use App\Models\Product;
use App\Models\Studyplan;
use App\Models\Websitesetting;
use App\Repositories\ProductRepository;
use App\Traits\MetaCommon;
use App\Traits\ProductCommon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductLandingController extends Controller
{
    use ProductCommon;
    use MetaCommon;

    private $setting;

    public function __construct(Websitesetting $setting)
    {
        $this->setting = $setting->setting;
    }


    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function landing1(Request $request): RedirectResponse
    {
        return redirect()->route('api.v2.landing.6', $request->all());
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function landing2(Request $request): RedirectResponse
    {
        return redirect()->route('api.v2.landing.5', $request->all());
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function landing3(Request $request): RedirectResponse
    {
        return redirect()->route('api.v2.landing.5', $request->all());
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function landing4(Request $request): RedirectResponse
    {
        return redirect()->route('api.v2.landing.5', $request->all());
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function landing5(Request $request)
    {
        $product_ids = [
            328,
            230,
            222,
            213,
            210,
            232,
            234,
            236,
            242,
            240,
            408,
        ];
        $reshteIdArray = [
            242 => 'riazi',
            240 => 'tajrobi',
            408 => 'riazi tajrobi ensani',
            236 => 'riazi tajrobi ensani',
            230 => 'riazi tajrobi',
            234 => 'tajrobi',
            232 => 'riazi tajrobi',
            222 => 'ensani',
            210 => 'riazi tajrobi ensani',
            213 => 'tajrobi',
            328 => 'tajrobi',
        ];

        $products = Cache::remember('api-v2-landing-5-products', config('constants.CACHE_600'),
            function () use ($product_ids, $reshteIdArray) {
                $products = Product::whereIn('id', $product_ids)
                    ->orderBy('order')
                    ->enable()
                    ->get();

                $products->map(function ($product) use ($reshteIdArray) {
                    $product['type'] = $reshteIdArray[$product->id];
                    return $product;
                });

                return $products;
            });

        return ProductInLandingWithoutPagination::collection($products)->response();
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function landing6(Request $request): RedirectResponse
    {
        return redirect()->route('api.v2.landing.9', $request->all());
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function landing7(Request $request): RedirectResponse
    {
        return redirect()->route('api.v2.landing.9', $request->all());
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function landing8(Request $request): JsonResponse
    {
        $block = Block::where('id', 138)->get();
        $resource = [
            'block' => $block,
            'plan' => null,
        ];

        return (new ProductLandingResource($resource))->response();
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function landing9(Request $request)
    {
        $block = Block::where('id', 136)->get();

        $studyPlan = Studyplan::all();

        $key = 'studyPlan-taftan99';
        $studyPlan = Cache::remember($key, config('constants.CACHE_600'), function () use ($studyPlan) {
            return \App\Http\Resources\StudyPlan::collection($studyPlan)->resource;
        });

        $resource = [
            'block' => $block,
            'plan' => $studyPlan,
        ];

        return (new ProductLandingResource($resource))->response();
    }

    /**
     * Products Special Landing Page
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function landing10(Request $request)
    {
        $block = Block::where('id', 137)->get();
        $resource = [
            'block' => $block,
            'plan' => null,
        ];

        return (new ProductLandingResource($resource))->response();
    }

    public function landing17(Request $request)
    {
        $productIds = [
            444,
            447,
            448
        ];

        $landingProducts = Cache::tags([
            'landing', 'landing17', 'landing17_products', 'product'
        ])->remember('TelescopeProducts_appLanding', config('constants.CACHE_600'),
            static function () use ($productIds) {
                return ProductRepository::getProductsById($productIds)->orderBy('order')->get();
            });

        return ProductLandingResource::collection($landingProducts);
    }

    public function anareshtan(Request $request, Product $product): JsonResponse
    {
        $mobile = $request->user() ? $request->user()->id : '';
        $paymentLink = 'https://alaatv.com/paymentRedirect/parsian/web';
        $banner = [
            'enable' => true,
            'imgDesktopSrc' => 'https://nodes.alaatv.com/upload/landing/anarestan/anarestan_land_desk2.jpg',
            'imgMobileSrc' => 'https://nodes.alaatv.com/upload/landing/anarestan/anarestan_land_mobile2.jpg'
        ];

        $url = $request->url();

        $product = null;
        $products = Cache::remember('landing:anarestan:products', config('constants.CACHE_600'), function () {
            $products = Product::query()->whereIn('id', array_keys(Product::ALL_ABRISHAM_PRODUCTS))
                ->enable()
                ->orderBy('order')
                ->get();

            $moshavereProduct = Product::find(Product::SHOROO_AZ_NO);

            $products->prepend($moshavereProduct);

            return ProductIndex::collection($products);
        });

        $sets = Cache::remember('landing:anarestan:sets', config('constants.CACHE_600'), function () {
            $sets = Contentset::query()->whereIn('id', [1374, 588, 826, 982, 609, 1006])->get();

            return SetInIndex::collection($sets);
        });

        $responseData = [
            'mobile' => $mobile,
            'paymentLink' => $paymentLink,
            'banner' => $banner,
            'products' => $products,
            'sets' => $sets,
        ];

        return response()->json($responseData);
    }
}