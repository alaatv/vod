<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\CacheFlush;
use App\Classes\Search\OrderSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditOrderRequest;
use App\Http\Requests\InsertOrderRequest;
use App\Http\Resources\Admin\OrderResource;
use App\Http\Resources\AlaaJsonResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\BatchTransferNotification;
use App\Notifications\ProductChoiceAbrisham;
use App\Repositories\UserRepo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;


/**
 * Class OrderController.
 * For Api Version 2.
 * For Admin side.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class OrderController extends Controller
{
    /**
     * OrderController constructor.
     */
    public function __construct()
    {
        $authException = $this->getAuthExceptionArray();
        $this->callMiddlewares($authException);
    }

    /**
     *
     * @return array
     */
    private function getAuthExceptionArray(): array
    {
        return [];
    }

    /**
     * @param $authException
     */
    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
        $this->middleware('permission:'.config('constants.TRANSFER_ORDERS_OF_USER'),
            ['only' => ['orderBatchTransfer',],]);
        $this->middleware('permission:'.config('constants.LIST_ORDER_ACCESS'), ['only' => ['index']]);
        $this->middleware('permission:'.config('constants.INSERT_ORDER_ACCESS'), ['only' => ['store']]);
        $this->middleware('permission:'.config('constants.EDIT_ORDER_ACCESS'), ['only' => ['update']]);
        $this->middleware('permission:'.config('constants.REMOVE_ORDER_ACCESS'), ['only' => ['destroy']]);
        $this->middleware('permission:'.config('constants.SHOW_ORDER_ACCESS'), ['only' => ['show']]);
        $this->middleware('permission:'.config('constants.SEND_SMS_TO_USER_ACCESS'),
            ['only' => ['abrishamProductChoice']]);
    }

    /**
     * Return a listing of the resource.
     *
     * @param  OrderSearch  $orderSearch
     * @return ResourceCollection
     */
    public function index(OrderSearch $orderSearch)
    {
        // Set the number of items on each page.
        if (request()->has('length') && request()->length > 0) {
            $orderSearch->setNumberOfItemInEachPage(request()->get('length'));
        }

        // Filter resources based on received parameters.
        $orderResult = $orderSearch->get(request()->all());

        return OrderResource::collection($orderResult);
    }

    /**
     * Return the specified resource.
     *
     * @param  Order  $order
     * @return AlaaJsonResource|OrderResource|RedirectResponse|Redirector
     */
    public function show(Order $order)
    {
        return (new OrderResource($order));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertOrderRequest|Order  $request
     * @return JsonResponse
     */
    public function store(InsertOrderRequest $request)
    {
        if ($request->has('register_new_order')) {
            $request->merge([
//                'insertor_id' => $request->user()->id,
                'orderstatus_id' => config('constants.ORDER_STATUS_OPEN_BY_ADMIN'),
                'paymentstatus_id' => config('constants.PAYMENT_STATUS_UNPAID'),
                'couponDiscount' => 0,
                'couponDiscountAmount' => 0,
                'discount' => 0,
                'completed_at' => now('Asia/Tehran'),
            ]);
        }

        $order = new Order();

        $this->fillOrderFromRequest($request->all(), $order);

        try {
            $order->save();
            // there must save order products and another needed items.
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new OrderResource($order->refresh()))->response();
    }

    /**
     * Fill the model object to be stored or updated in database.
     *
     * @param  array  $inputData
     * @param  Order  $order
     */
    private function fillOrderFromRequest(array $inputData, Order $order): void
    {
        $order->fill($inputData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Order  $order
     * @return Exception|JsonResponse
     * @throws Exception
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json();
    }

    public function orderBatchTransfer(Request $request)
    {
        Validator::make($request->all(), [
            'originUserMobile' => ['required', 'string', 'min:1'],
            'originUserNationalCode' => ['required', 'string', 'min:1'],
            'destinationUserMobile' => ['required', 'string', 'min:1'],
            'destinationUserNationalCode' => ['required', 'string', 'min:1'],
        ])->validate();

        $authUser = $request->user();
        $originUserMobile = request('originUserMobile');
        $originUserNationalCode = request('originUserNationalCode');

        $destinationUserMobile = request('destinationUserMobile');
        $destinationUserNationalCode = request('destinationUserNationalCode');

        try {

            /** @var User $originUser */
            if (!$originUser = UserRepo::find($originUserMobile, $originUserNationalCode)->first()) {
                return response()->json([
                    'message' => 'Origin user not found',
                    'code' => 1
                ], Response::HTTP_BAD_REQUEST);
            }

            /** @var User $destinationUser */
            if (!$destinationUser = UserRepo::find($destinationUserMobile, $destinationUserNationalCode)->first()) {
                return response()->json([
                    'message' => 'Destination user not found',
                    'code' => 2
                ], Response::HTTP_BAD_REQUEST);
            }

            $originUserOrders = $originUser->orders;

            if ($originUserOrders->isEmpty()) {
                return response()->json([
                    'message' => 'Origin user has no orders',
                    'code' => 3
                ], Response::HTTP_BAD_REQUEST);
            }

            DB::transaction(function () use ($originUser, $destinationUser, $authUser) {
                $originUser->orders->each(function ($order) use ($destinationUser, $originUser, $authUser) {
                    $order->update(['user_id' => $destinationUser->id]);
                });

                $originUser->bankaccounts->each(function ($bankaccount) use ($destinationUser, $originUser, $authUser) {
                    $bankaccount->update(['user_id' => $destinationUser->id]);
                });

            });

            $receiver = '';
            if ($originUser->mobile != $destinationUser->mobile) {
                $receiver .= 'شماره '.$destinationUser->mobile;
            }

            if ($originUser->nationalCode != $destinationUser->nationalCode) {
                if (strlen($receiver) > 0) {
                    $receiver .= ' و ';
                }
                $receiver .= 'کد ملی '.$destinationUser->nationalCode;
            }

            if ($request->has('originNotification')) {
                $originUser->notify(new BatchTransferNotification($receiver));
            }

            CacheFlush::flushAssetCache($originUser);
            CacheFlush::flushAssetCache($destinationUser);

            return response()->json(['message' => 'Orders transferred successfully'], Response::HTTP_OK);

        } catch (Exception $exception) {
            Log::error('Orders batch transfer error. error:'.$exception->getMessage().' .file :'.$exception->getFile().' .line: '.$exception->getLine());
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE,
                'Orders transfer failed.'.$exception->getMessage().' .file :'.$exception->getFile().' .line: '.$exception->getLine());
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditOrderRequest|Order  $request
     * @param  Order  $order
     * @return JsonResponse
     */
    public function update(EditOrderRequest $request, Order $order)
    {
        $this->fillOrderFromRequest($request->all(), $order);

        try {
            $order->update($request->all());
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new OrderResource($order->refresh()))->response();
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function abrishamProductChoice(Request $request): JsonResponse
    {
        $abrishamPackIds = [
            Product::RAHE_ABRISHAM99_PACK_TAJROBI,
            Product::RAHE_ABRISHAM99_PACK_RIYAZI
        ];
        $abrishamPhysicIds = [
            Product::RAHE_ABRISHAM99_FIZIK_RIYAZI,
            Product::RAHE_ABRISHAM99_FIZIK_TAJROBI
        ];
        $orderStatusIds = config('constants.ORDER_STATUS_CLOSED');
        $paymentStatusIds = [
            config('constants.PAYMENT_STATUS_PAID'),
            config('constants.PAYMENT_STATUS_INDEBTED'),
            config('constants.PAYMENT_STATUS_VERIFIED_INDEBTED'),
        ];

        $orders = Order::whereHas('orderproducts', function ($q) use ($abrishamPackIds) {
            $q->whereIn('product_id', $abrishamPackIds);
        })
            ->whereDoesntHave('orderproducts', function ($q) use ($abrishamPhysicIds) {
                $q->whereIn('product_id', $abrishamPhysicIds);
            })
            ->where('orderstatus_id', $orderStatusIds)
            ->whereIn('paymentstatus_id', $paymentStatusIds)
            ->get();

        /** @var Order $order */
        foreach ($orders as $order) {
            $order->user->notify(new ProductChoiceAbrisham('پک کامل دروس تخصصی راه ابریشم',
                'راه ابریشم فیزیک آقای طلوعی', '1005', 'راه ابریشم فیزیک آقای کازرانیان', '1006'));
        }

        return response()->json(['message' => 'عملیات موفق.'], Response::HTTP_OK);
    }
}
