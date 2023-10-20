<?php

namespace App\Http\Controllers\Api;

use App\Classes\JWT;
use App\Http\Controllers\Controller;
use App\Http\Resources\Ewano\EwanoOrderResource;
use App\Http\Resources\User as ResourcesUser;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\User;
use App\Services\EwanoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;


class EwanoController extends Controller
{
    public function root(Request $request)
    {
        $token = EwanoService::GetToken($request->get('id'));
        $userEwano = EwanoService::UserInquiry(Arr::get($token, 'token'));
        $user = User::firstOrCreate(
            [
                'mobile' => '0'.Arr::get($userEwano, 'attributes.msisdn'),
                'nationalCode' => Arr::get($userEwano, 'attributes.nationalCode')
            ],
            [
                'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                'password' => bcrypt(Arr::get($userEwano, 'attributes.nationalCode')),
            ]
        );
        $decodecToken = JWT::decode($token, verify: false);
        $user->ewanoUser()->updateOrCreate(
            [
                'ewano_user_id' => $request->get('id'),
                'user_id' => $decodecToken->data->userId,
            ],
            [
                'refresh_token' => Arr::get($token, 'refreshToken'),
            ]
        );
        $token = $user->getAppToken();
        $data = array_merge([
            'user' => new ResourcesUser($user),
        ], $token);
        return response()->json([
            'data' => $data,
        ], Response::HTTP_OK);
    }

    public function makeOrder()
    {
        $user = auth()->user();
        $ewanoUser = $user->ewanoUser;
        $refreshToken = $ewanoUser?->refresh_token;
        if (!isset($refreshToken)) {
            return new EwanoOrderResource(null);
        }
        $order = auth()->user()->openOrder(0, 1);
        if (!$order) {
            return myAbort(\Symfony\Component\HttpFoundation\Response::HTTP_METHOD_NOT_ALLOWED, 'سبد خرید خالی است');
        }
        $ewanoOrder = EwanoService::CreateOrder($user, $order, $refreshToken);
        $ewanoUserOrder = $ewanoUser->orders()->create([
            'order_id' => $order->id,
            'third_party_order_id' => $ewanoOrder->result->data->id,
        ]);
        $orderProductsArray = [];
        foreach ($ewanoOrder->result->data->items as $item) {
            $alaaOrderProduct = Orderproduct::whereHas('product', function ($query) use ($item) {
                $query->where('name', $item->title);
            })->whereHas(
                'order',
                function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->where('orderstatus_id', config('constants.ORDER_STATUS_OPEN'))
                        ->where('paymentstatus_id', config('constants.PAYMENT_STATUS_UNPAID'));
                }
            )->first();
            $thirdPartyOrderProductDetail = [
                'order_product_id' => $alaaOrderProduct->id,
                'third_party_product_item_id' => $item->id,
            ];
            $orderProductsArray[] = $thirdPartyOrderProductDetail;
        }
        $ewanoUserOrder->orderProducts()->createMany($orderProductsArray);
        $order->update([
            'orderstatus_id' => config('constants.ORDER_STATUS_CLOSED')
        ]);
        return new EwanoOrderResource($ewanoOrder);
    }

    public function pay(Request $request)
    {
        $user = auth()->user();
        $ewanoUser = $user->ewanoUser;
        $ewanoOrderId = $request->input('ewano_order_id');
        $alaaOrder = Order::find($ewanoOrderId);
        if (!$alaaOrder) {
            return response()->json([
                'data' => [
                    'status' => 'Failed',
                    'message' => 'سفارش شما یافت نشد. پرداخت ناموفق',
                ],
            ]);
        }
        $alaaOrder->update(['paymentstatus_id' => config('constants.PAYMENT_STATUS_PAID')]);
        $pay = EwanoService::Pay($user, $ewanoOrderId, $ewanoUser->refresh_token);
        if ($pay && $pay->result->status->code == 200) {
            return response()->json([
                'data' => [
                    'status' => 'OK',
                    'message' => 'پرداخت موفقیت آمیز',
                ],
            ]);
        }
        return response()->json([
            'data' => [
                'status' => 'Failed',
                'message' => 'پرداخت ناموفق',
            ]
        ]);
    }
}
