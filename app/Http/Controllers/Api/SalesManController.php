<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReferralCode;
use App\Models\ReferralCode;
use App\Models\SalesManProfile;
use App\Models\SalesManProfile;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SalesManController extends Controller
{
    public function index(Request $request, WalletService $walletService): JsonResponse
    {
        $user = auth()->user();
        $wallets = $walletService->getWalletsByUserId($user->id);
        $walletData = Arr::get($wallets, 'data', []);
        $userMainWallet = array_filter_collapse($walletData, function ($wallet) {
            return $wallet['withdrawable'] == true;
        });
        $walletType = !empty($userMainWallet) ? $userMainWallet['type'] : null;
        $walletBalance = !empty($userMainWallet) ? $userMainWallet['available-asset'] : null;
        $income = $walletService->getUserTotalIncomebyUserId($user->id);
        $incomeData = Arr::get($income, 'data', []);
        $totalCommission = !empty($incomeData) ? $incomeData['total-income'] : null;
        $hasSignedContract =
            (bool) SalesManProfile::where('user_id', $request->user()->id)->first()?->hasSignedContract;
        $minAmountUntilSettlement = config('constants.MIN_AMOUNT_UNTIL_SETTLEMENT');
        $referralCodes = ReferralCode::whereHas('referralRequest', function ($query) use ($user) {
            $query->where('owner_id', $user->id);
        });
        $countOfTotalGiftCards = $referralCodes->count();
        $countOfUsedGiftCards = with(clone $referralCodes)->used(1)->sold()->count();
        $countOfUsedWithoutPayGiftCards = with(clone $referralCodes)->used(1)->notSold()->count();
        $countOfRemainGiftCards = with(clone $referralCodes)->assigned(0)->used(0)->count();
        $countOfUnusedWithAssigneeGiftCards = with(clone $referralCodes)->assigned(1)->used(0)->count();
        $pendingIncome = $walletService->getUserTotalPendingIncomeByUserId($user->id);
        $pendingIncomeData = Arr::get($pendingIncome, 'data', []);
        $incomeBeingSettle =
            !empty($pendingIncomeData) ? $pendingIncomeData['total-pending-withdraw-request'] : null;
        return response()->json([
            'wallet_type' => $walletType,
            'wallet_balance' => $walletBalance,
            'total_commission' => $totalCommission,
            'has_signed_contract' => $hasSignedContract,
            'minAmount_until_settlement' => $minAmountUntilSettlement,
            'count_of_total_gift_cards' => $countOfTotalGiftCards,
            'count_of_used_gift_cards' => $countOfUsedGiftCards,
            'count_of_remain_gift_cards' => $countOfRemainGiftCards,
            'income_being_settle' => $incomeBeingSettle,
            'count_of_used_without_pay_gift_cards' => $countOfUsedWithoutPayGiftCards,
            'count_of_unused_with_assignee_gift_cards' => $countOfUnusedWithAssigneeGiftCards,
        ]);
    }

    public function submitContract(Request $request)
    {
        $request->user()->salesManProfile()->update(['hasSignedContract' => true]);
        return response()->json();
    }
}
