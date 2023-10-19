<?php

namespace App\Services;

use App\Helpers\GuzzleRequest;

class WalletService
{
    public function __construct()
    {
    }

    public function getUserWallets(string $userToken)
    {
        $token = [
            'user-token' => $userToken,
        ];
        return GuzzleRequest::send(
            'GET',
            config('services.accounting.server').'/api/v1/user/users/wallets',
            headers: defaultHeader() + $token,
        );
    }

    public function getUserTotalIncomebyUserId(int $userId)
    {
        return GuzzleRequest::send(
            'GET',
            config('services.accounting.server')."/api/v1/service/users/$userId/transactions/total-income",
            headers: defaultHeader()
        );
    }

    public function getUserTotalPendingIncomeByUserId(int $userId)
    {
        return GuzzleRequest::send(
            'GET',
            config('services.accounting.server')."/api/v1/service/users/$userId/withdraw-requests/total-pending",
            headers: defaultHeader()
        );
    }

    public function getWalletsByUserId(int $userId)
    {

        return GuzzleRequest::send(
            'GET',
            config('services.accounting.server')."/api/v1/service/users/{$userId}/wallets",
            headers: defaultHeader()
        );
    }

    public function withdrawRequest(int $userId, float $amount, int $walletId, int $bankAccountId)
    {
        return GuzzleRequest::send(
            'POST',
            config('services.accounting.server')."/api/v1/service/users/$userId/wallets/$walletId/bank-accounts/$bankAccountId/withdraw-requests",
            [
                'amount' => (int) round($amount),
            ],
            defaultHeader()
        );
    }

    public function getWithdrawRequests(int $userId)
    {
        return GuzzleRequest::send(
            'GET',
            config('services.accounting.server')."/api/v1/service/users/$userId/withdraw-requests",
            headers: defaultHeader()
        );
    }

    public function createTransactionForWalletByUserId(
        int $userId,
        int $walletId,
        float $amount,
        string $reason,
        string $reasonType,
        string $description = ' '
    ) {
        return GuzzleRequest::send(
            'POST',
            config('services.accounting.server')."/api/v1/service/users/$userId/wallets/$walletId/transactions",
            [
                'amount' => $amount,
                'reason' => $reason,
                'reason-type' => $reasonType,
                'description' => $description,
            ],
            defaultHeader()
        );
    }
}
