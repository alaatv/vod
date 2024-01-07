<?php

namespace App\Http\Controllers\Api;

use App\Classes\CacheFlush;
use App\Events\SendOrderNotificationsEvent;
use App\Events\UserPurchaseCompleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\HekmatVoucher as VerifyVoucherResource;
use App\Models\Coupon;
use App\Models\Productvoucher;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\ProductvoucherRepo;
use App\Repositories\TransactionRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function __construct()
    {
        $authException = $this->getAuthExceptionArray();
        $this->callMiddlewares($authException);
    }

    private function getAuthExceptionArray(): array
    {
        return [];
    }

    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
        $this->middleware('permission:'.config('constants.VERIFY_HEKMAT_VOUCHER'), ['only' => ['verify']]);
        $this->middleware('permission:'.config('constants.DISABLE_HEKMAT_VOUCHER'), ['only' => ['disable']]);
        $this->middleware(['findVoucher', 'validateVoucher'], ['only' => ['submit']]);
    }

    public function verify(Request $request)
    {
        $voucher = ProductvoucherRepo::findVoucherByCode($request->get('code'))->first();
        if (! isset($voucher)) {
            return response()->json([
                'error' => 'Resource not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return new VerifyVoucherResource($voucher);
    }

    public function disable(Request $request)
    {
        $voucher = ProductvoucherRepo::findVoucherByCode($request->get('code'))->first();
        if (! isset($voucher)) {
            return response()->json([
                'error' => 'Resource not found',
            ], Response::HTTP_NOT_FOUND);
        }
        if (ProductvoucherRepo::disableVoucher($voucher)) {
            return response()->json([
                'date' => [
                    'message' => 'ووچر با موفقیت غیر فعال شد',
                ],
            ]);
        }

        return response()->json([
            'error' => [
                'message' => 'Server error',
            ],
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function submit(Request $request)
    {
        $user = $request->user();
        $voucher = $request->get('voucher');
        $code = $voucher->code;
        $products = $voucher->products;
        $coupon = $voucher->coupon_id;
        [$done, $order] = $this->addVoucherProductsToUser($user, $products, $coupon);
        if ($done) {
            $voucher->markVoucherAsUsed($user->id, $order->id, Productvoucher::CONTRANCTOR_HEKMAT);
            event(new SendOrderNotificationsEvent($order, $user, true, true));
            event(new UserPurchaseCompleted($order));

            return response()->json([
                'message' => 'Voucher has been used successfully',
                'products' => $products,
            ]);

        }

        return myAbort(Response::HTTP_BAD_REQUEST, 'Your request could not been done', ['code' => $code]);
    }

    private function addVoucherProductsToUser(User $user, Collection $products, $couponId): array
    {
        $coupon = Coupon::find($couponId);
        if (! isset($coupon)) {
            return [false, null];
        }
        $order = OrderRepo::createBasicCompletedOrder(
            $user->id,
            config('constants.PAYMENT_STATUS_ORGANIZATIONAL_PAID'),
            null,
            null,
            $coupon->id,
            $coupon->discount
        );
        foreach ($products as $product) {
            $price = $product->price;
            OrderproductRepo::createBasicOrderproduct($order->id, $product->id, $price['base'], $price['base']);
        }
        try {
            $order->discount = 0;
            $finalPriceArray = $order->obtainOrderCost(true, false);
            $finalPrice = $finalPriceArray['totalCost'];
            $order->update([
                'cost' => $finalPrice,
            ]);

            $eachInstalment = floor($finalPrice / 12);
            $lastInstalment = $finalPrice % 12;
            for ($i = 1; $i <= 12; $i++) {
                if ($i == 12) {
                    $eachInstalment += $lastInstalment;
                }

                TransactionRepo::createBasicTransaction($order->id, $eachInstalment, 'قسط حکمت');
            }
            CacheFlush::flushAssetCache($user);
            $result = [true, $order];
        } catch (Exception $e) {
            $order->delete();
            Log::error('submitVoucher:addVoucherProductsToUser:generateOrderInvoice');
            Log::error('file:'.$e->getFile().':'.$e->getLine());
            Log::error('error:'.$e->getMessage());
            $result = [false, null];
        }

        return $result;
    }
}
