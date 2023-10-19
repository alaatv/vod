<?php

namespace App\Services;

use App\Classes\ReferralCodeSubmitter;
use App\Models\Product;
use App\Models\ReferralCode;
use App\Models\ReferralCode;
use App\Models\ReferralRequest;
use App\Models\ReferralRequest;
use App\Repositories\OrderRepo;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderService
{

    public function __construct(public OrderRepo $orderRepo, public OrderProductsService $orderProductsService)
    {
    }

    public function createIrancellOrderWithReferralCode()
    {
        return DB::transaction(function () {
            $order = auth()->user()->orders()->where([
                'orderstatus_id' => config('constants.ORDER_STATUS_OPEN_IRANCELL'),
                'paymentstatus_id' => config('constants.PAYMENT_STATUS_UNPAID')
            ])->first();
            $order = $order ?? OrderRepo::createBasicCompletedOrder(auth()->id(),
                orderStatusId: config('constants.ORDER_STATUS_OPEN_IRANCELL'));
            if (!$order->referralCode) {
                $referralRequest = ReferralRequest::findOrFail(config('constants.IRNACELL_REFERRAL_REQUEST_ID'));
                $referralCode = $referralRequest->referralCodes()->create([
                    'owner_id' => $referralRequest->owner_id,
                    'code' => randomNumber(2).'-'.randomNumber(5),
                    'enable' => 1,
                ]);
                $referralRequest->increment('numberOfCodes');
                $this->validateReferralCode($referralCode);
                (new ReferralCodeSubmitter($order))->submit($referralCode);
            }
            return $order;
        });
    }

    public function validateReferralCode(ReferralCode $referralCode)
    {
        $referralCodeValidationStatus = $referralCode->validateReferralCode();
        if ($referralCodeValidationStatus != ReferralCode::REFERRAL_CODE_VALIDATION_STATUS_OK) {
            throw new UnprocessableEntityHttpException(ReferralCode::REFERRAL_CODE_VALIDATION_INTERPRETER[$referralCodeValidationStatus] ?? 'Referral code validation status is undetermined');
        }
        return true;
    }

    public function createOpenOrderWithBasicOrderProduct(
        int $userId,
        int $productId,
        int $includedInCoupon = 0,
        int $discountPercentage = 0,
        int $includedInInstalments = 0
    ) {
        return DB::transaction(function () use (
            $userId,
            $productId,
            $includedInCoupon,
            $discountPercentage,
            $includedInInstalments
        ) {
            $productPrice = Product::find($productId)->price;
            $order =
                $this->orderRepo::createBasicCompletedOrder(userId: $userId, costWithCoupon: 0,
                    costWithoutCoupon: $productPrice['final']);
            $this->orderProductsService->orderproductRepo::createBasicOrderproduct(
                $order->id,
                $productId,
                $productPrice['base'],
                $productPrice['final'],
                $includedInCoupon,
                $discountPercentage,
                $includedInInstalments
            );
            return $order;
        });
    }
}
