<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BankAccountService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService)
    {
    }

    public function withdrawWallet(Request $request, BankAccountService $bankAccountService)
    {
        $user = $request->user();
        $wallets = $this->walletService->getWalletsByUserId($user->id);
        $wallet = array_filter_collapse($wallets['data'], function ($wallet) {
            return $wallet['withdrawable'] == true;
        });
        $bankAccounts = $bankAccountService->getUserBankAccounts($user->id);
        $bankAccount = array_filter_collapse($bankAccounts['data'], function ($bankAccount) {
            return ($bankAccount['verify'] == true && $bankAccount['status'] == 'verify');
        });

        if (!$bankAccount) {
            return myAbort(Response::HTTP_BAD_REQUEST, '.حساب بانکی یافت نشد. لطفا حساب بانکی خود را وارد کنید');
        }
        $returnValue = match (true) {
            ($wallet['available-asset'] <= config('constants.MIN_AMOUNT_UNTIL_SETTLEMENT')) => 'موجودی کافی نمی باشد.',
            !isset($bankAccount['sheba']) => 'شماره شبا ثبت نشده است.',
            !isset($user->kartemeli) => 'عکس کارت ملی را آپلود نکرده اید.',
            default => null,
        };
        if ($returnValue) {
            return myAbort(Response::HTTP_BAD_REQUEST, $returnValue);
        }
        return $this->walletService->withdrawRequest($user->id, $wallet['available-asset'], $wallet['id'],
            $bankAccount['id']);
    }


    public function withdrawRequests()
    {
        return $this->walletService->getWithdrawRequests(auth()->id());
    }
}
