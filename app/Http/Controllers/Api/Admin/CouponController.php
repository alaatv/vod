<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\CouponSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRandomMassiveCouponRequest;
use App\Http\Requests\EditCouponRequest;
use App\Http\Requests\InsertCouponRequest;
use App\Http\Requests\Request;
use App\Http\Requests\StorePenaltyCoupon;
use App\Http\Resources\Admin\CouponLightResource;
use App\Http\Resources\Admin\CouponResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Coupon;
use App\Models\Coupontype;
use App\Repositories\CouponRepo;
use App\Repositories\Loging\ActivityLogRepo;
use App\Traits\CouponCommon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

/**
 * Class CouponController.
 * For Api Version 2.
 * For Admin side.
 */
class CouponController extends Controller
{
    use CouponCommon;

    public function __construct()
    {
        //        $this->middleware('permission:'.config('constants.LIST_COUPON_ACCESS'), ['only' => 'index']);
        //        $this->middleware('permission:'.config('constants.INSERT_COUPON_ACCESS'), ['only' => 'store', 'update']);
        //        $this->middleware('permission:'.config('constants.REMOVE_COUPON_ACCESS'), ['only' => 'destroy']);
        //        $this->middleware('permission:'.config('constants.SHOW_COUPON_ACCESS'), ['only' => 'show']);
    }

    /**
     * Return a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(CouponSearch $couponSearch)
    {
        // Set the number of items on each page.
        if (request()->has('length') && request()->get('length') > 0) {
            $couponSearch->setNumberOfItemInEachPage(request()->get('length'));
        }

        // Filter resources based on received parameters.
        $couponResult = $couponSearch->get(request()->all());

        return CouponResource::collection($couponResult);
    }

    /**
     * Return the specified resource.
     *
     * @return JsonResponse|CouponResource|RedirectResponse|Redirector
     */
    public function show(Coupon $coupon)
    {
        return new CouponResource($coupon);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return CouponResource|JsonResponse
     *
     * @throws Exception
     */
    public function store(InsertCouponRequest $request)
    {
        // set coupon random code
        if (! isset($request->code)) {
            $threshold = Coupon::GENERATE_RANDOM_CODE_THRESHOLD;
            $coupons = Coupon::all();
            do {
                if ($threshold-- <= 0) {
                    return response()->json(['message' => 'کدی یافت نشد!'], Response::HTTP_SERVICE_UNAVAILABLE);
                }
                $code = random_int(10000, 99999);
            } while ($coupons->where('code', $code)->isNotEmpty());
            $request->offsetSet('code', $code);
        }

        // set coupon type
        $couponType = $request->has('products') && ! empty($request->get('products'))
            ? Coupontype::ATTRIBUTE_TYPE_PARTIAL_ID
            : Coupontype::ATTRIBUTE_TYPE_OVERALL_ID;
        $request->offsetSet('coupontype_id', $couponType);

        $coupon = new Coupon();
        $coupon->fill($request->all());
        $coupon->required_products = isset($request->required_products) ? array_map('intval',
            $coupon->required_products) : null;
        $coupon->unrequired_products = isset($request->unrequired_products) ? array_map('intval',
            $coupon->unrequired_products) : null;

        $coupon->discount = preg_replace('/\s+/', '', $coupon->discount);
        if ($coupon->discount == '') {
            $coupon->discount = 0;
        }

        $coupon->maxCost = preg_replace('/\s+/', '', $coupon->maxCost);
        if ($coupon->maxCost == '') {
            $coupon->maxCost = null;
        }
        if (! $coupon->save()) {
            return response()->json([
                'message' => 'خطا در درج کد',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $coupon->products()->sync($request->input('products', []));
        ActivityLogRepo::LogCouponCreation($request->user(), $coupon, $request->input('products', []));

        return new CouponResource($coupon->refresh());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditCouponRequest|Coupon  $request
     * @return JsonResponse
     */
    public function update(EditCouponRequest $request, Coupon $coupon)
    {
        $this->fillCouponFromRequest($request->all(), $coupon);

        try {
            $coupon->update();
            $coupon->products()->sync($request->products);
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new CouponResource($coupon->refresh()))->response();
    }

    /**
     * Fill the model object to be stored or updated in database.
     *
     * @param  array|Request  $inputData
     */
    private function fillCouponFromRequest(array $inputData, Coupon $coupon): void
    {
        $coupon->fill($inputData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Exception|JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json([
            'message' => 'coupon deleted successfully',
        ]);
    }

    /**
     * @param  Request|Coupon  $request
     * @return ResourceCollection
     */
    public function findByCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'min:2'],
        ]);

        $coupons = Coupon::where('code', 'like', "%{$request->code}%")->get();

        return CouponLightResource::collection($coupons);
    }

    /**
     * @return JsonResponse|ResourceCollection
     *
     * @throws Exception
     */
    public function generateMassiveRandomCoupon(CreateRandomMassiveCouponRequest $request)
    {
        $codePreFix = $request->get('codePrefix', '');
        $isStrict = $request->get('is_strict', 0);
        $codeLength = 6;
        $type =
            $request->has('hasLimitiedProducts') ? config('constants.COUPON_TYPE_PARTIAL') : config('constants.COUPON_TYPE_OVERALL');

        $numberOfCodes = $request->get('number');
        $coupons = collect();
        for ($i = 1; $i <= $numberOfCodes; $i++) {
            $threshold = Coupon::GENERATE_RANDOM_CODE_THRESHOLD;
            do {
                if ($threshold-- <= 0) {
                    return response()->json(['message' => 'تمام کدهای '.$codeLength.' رقمی ممکن تولید شده اند'],
                        Response::HTTP_BAD_REQUEST);
                }
                $randomString = generateRandomString($codeLength);
                $code = $codePreFix.$randomString;
            } while (CouponRepo::findCouponByCode($code));

            $coupon = Coupon::create(array_merge($request->validated(), [
                'code' => $code,
                'coupontype_id' => $type,
                'discounttype_id' => config('constants.DISCOUNT_TYPE_PERCENTAGE'),
                'is_strict' => $isStrict,
            ]));

            if ($type == config('constants.COUPON_TYPE_PARTIAL') && $request->has('products')) {
                $coupon->products()->sync($request->get('products', []));
                ActivityLogRepo::LogCouponCreation($request->user(), $coupon, $request->get('products', []));
            } else {
                ActivityLogRepo::LogCouponCreation($request->user(), $coupon, $request->get('products', 'all'));
            }
            $coupons->push($coupon);
        }

        return CouponResource::collection($coupons);
    }

    public function savePenaltyCoupon(StorePenaltyCoupon $request)
    {
        $coupons = $request->input('coupon');
        array_walk($coupons, function (&$value) {
            $value['usageLimit'] = 1;
            $value['discounttype_id'] = config('constants.DISCOUNT_TYPE_PERCENTAGE');
        });
        Coupon::insert($coupons);
    }
}
