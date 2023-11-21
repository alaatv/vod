<?php

namespace App\Http\Controllers\Api;

use App\Classes\Pricing\Alaa\AlaaInvoiceGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderProduct\OrderproductBatchExtend;
use App\Http\Requests\OrderProduct\OrderproductBatchRequestForExtension;
use App\Http\Requests\OrderProduct\OrderProductStoreRequest;
use App\Http\Requests\RestoreOrderproductRequest;
use App\Http\Resources\InvoiceWithoutItems as InvoiceResource;
use App\Models\Eventresult;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\OrderProductRenewal;
use App\Models\Product;
use App\Models\User;
use App\Traits\OrderCommon;
use App\Traits\OrderproductTrait;
use App\Traits\ProductCommon;
use App\Traits\RequestCommon;
use App\Traits\UserCommon;
use Cache;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderproductController extends Controller
{
    use ProductCommon;
    use OrderproductTrait;
    use OrderCommon;
    use RequestCommon;
    use UserCommon;

    public function __construct()
    {
        $this->middleware(['OverwriteOrderIDAndAddItToRequest',], ['only' => ['store', 'storeV2'],]);
        $this->middleware('permission:'.config('constants.RESTORE_ORDERPRODUCT_ACCESS'), ['only' => 'restore']);
    }

    /**
     * API Version 2
     *
     * @param  OrderProductStoreRequest  $request
     *
     * @return JsonResponse
     */
    public function storeV2(OrderProductStoreRequest $request)
    {
        $productId = $request->get('product_id');
        $product = Product::with('complimentedproducts')->findOrFail($productId);
        if ($product->seller != $request->get('seller', config('constants.ALAA_SELLER'))) {
            return myAbort(Response::HTTP_LOCKED, 'این محصول آلاء نمی باشد');
        }
        $order = Order::with('orderproducts')->whereId($request->get('order_id'))->get()->first();
        if ($product->complimentedproducts->isNotEmpty()) {
            foreach ($product->complimentedproducts as $complimentedproduct) {
                if ($complimentedproduct->pivot->is_dependent && !$order->orderproducts->contains('product_id',
                        $complimentedproduct->id)) {
                    return myAbort(Response::HTTP_FORBIDDEN, 'این محصول وابسته به محصول دیگری است');
                }
            }
        }
        Cache::tags(['order_'.$request->get('order_id')])->flush();

        $user = $request->user();
        if (!($request->has('extraAttribute') && !$user->isAbleTo(config('constants.ATTACH_EXTRA_ATTRIBUTE_ACCESS')))) {
            $this->new($request->all());

            return response()->json(null);
        }

        $attributesValues = $this->getAttributesValuesFromProduct($request, $request->input('product'));
        $this->syncExtraAttributesCost($request, $attributesValues);
        $request->offsetSet('parentProduct', $request->input('product'));

        if ($request->get('has_instalment_option')) {// delete all of the orderproducts before adding a new one
            $orderproductIds = $order->orderproducts->pluck('id')->toArray();
            Orderproduct::whereIn('id', $orderproductIds)->delete();
        }

        $this->new($request->all());

        return response()->json(null);
    }

    /**
     * API Version 2
     *
     * @param  Request  $request
     * @param  Orderproduct  $orderproduct
     *
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroyV2(Request $request, Orderproduct $orderproduct)
    {
        $authenticatedUser = $request->user();
        $orderUser = optional(optional($orderproduct)->order)->user;

        if ($authenticatedUser->id != $orderUser->id) {
            return myAbort(Response::HTTP_FORBIDDEN, 'Orderproduct does not belong to this user.');
        }

        $orderproduct_userbons = $orderproduct->userbons;
        foreach ($orderproduct_userbons as $orderproduct_userbon) {
            $orderproduct_userbon->usedNumber =
                $orderproduct_userbon->usedNumber - $orderproduct_userbon->pivot->usageNumber;
            $orderproduct_userbon->userbonstatus_id = config('constants.USERBON_STATUS_ACTIVE');
            if ($orderproduct_userbon->usedNumber >= 0) {
                $orderproduct_userbon->update();
            }
        }

        if (!$orderproduct->delete()) {

            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, 'Database error on removing orderproduct');
        }
        foreach ($orderproduct->children as $child) {
            $child->delete();
        }

        Cache::tags([
            'order_'.$orderproduct->order_id.'_products',
            'order_'.$orderproduct->order_id.'_orderproducts',
            'order_'.$orderproduct->order_id.'_cost',
            'order_'.$orderproduct->order_id.'_bon',
        ])->flush();

        /** @var Order $order */
        $order = $authenticatedUser->getOpenOrderOrCreate();

        $credit = $authenticatedUser->getTotalWalletBalance();
        $orderHasDonate = $order->hasDonate();

        $invoiceInfo = (new AlaaInvoiceGenerator())->generateOrderInvoice($order);
        $coupon = $order->coupon_info2;
        $coupon = Arr::get($coupon, 0);
        $fromWallet = min($invoiceInfo['price']['payableByWallet'], $credit);

        $invoiceInfo = array_merge($invoiceInfo, [
            'coupon' => $coupon,
            'orderHasDonate' => $orderHasDonate,
            'payByWallet' => $fromWallet,
        ]);

        return (new InvoiceResource($invoiceInfo))->response();
    }

    public function batchExtensionRequest(OrderproductBatchRequestForExtension $request)
    {
        /** @var User $user */
        $user = $request->user();

        $file = $this->getRequestFile($request->all(), 'photo');
        if ($file) {
            $user->kartemeli = $this->storePhotoOfKartemeli($user, $file);
            $user->updateWithoutTimestamp();
        }

        DB::transaction(function () use ($request, $user) {
            Eventresult::firstOrCreate(['user_id' => $user->id, 'event_id' => $request->get('konkurYear')], [
                'eventresultstatus_id' => 1,
                'participant_group_id' => $request->get('studentOrGraduate'),
            ]);

            $order_products = $request->order_products
                ->pick(['id' => 'orderproduct_id', 'expire_at' => 'expired_at'])
                ->toArray();

            OrderProductRenewal::insert($order_products);
        });

        return response()->json();
    }

    public function batchExtend(OrderproductBatchExtend $request)
    {
        try {
            DB::beginTransaction();

            $order_products = Orderproduct::whereIn('id', $request->orderproducts);
            $order_products->update([
                'expire_at' => now()->addYear()
            ]);

            $order_products_ids = $order_products->pluck('id');

            OrderProductRenewal::whereIn('orderproduct_id', $order_products_ids)->notAccepted()->update([
                'accepted_at' => now(),
                'accepted_by' => auth()->id,
            ]);

            DB::commit();
            return response()->json();
        } catch (Exception $exception) {
            Db::rollBack();
            return response()->json([
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Http internal server error',
            ], 500);
        }

    }

    public function restore(RestoreOrderproductRequest $request)
    {
        $orderProduct = Orderproduct::onlyTrashed()->find($request->get('orderproductId'));
        if (!isset($orderProduct)) {
            return response()->json([
                'error' => [
                    'code' => Response::HTTP_NOT_FOUND, 'message' => 'Orderproduct not found'
                ]
            ], Response::HTTP_NOT_FOUND);
        }
        try {
            // Restore deleted order's product
            $orderProduct->restore();
            // Recalculate order cost after restore deleted order's product
            $orderProduct->updateOrderCost();
        } catch (Exception $exception) {
            return response()->json([
                'error' => [
                    'code' => Response::HTTP_SERVICE_UNAVAILABLE, 'message' => 'Unexpected error'
                ]
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        return response()->json(['message' => 'Orderproduct restored successfully'], Response::HTTP_OK);
    }
}
