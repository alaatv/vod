<?php

namespace App\Services;

use App\Helpers\GuzzleRequest;

class BankAccountService
{
    public function store(int $userId, string $sheba, string $cardNumber)
    {
        return GuzzleRequest::send(
            'POST', config('services.accounting.server')."/api/v1/service/users/$userId/bank-accounts",
            [
                'sheba' => $sheba,
                'card-number' => $cardNumber,
            ],
            defaultHeader(),
        );

    }

    public function getUserBankAccounts(int $userId)
    {
        return GuzzleRequest::send(
            'GET',
            config('services.accounting.server')."/api/v1/service/users/$userId/bank-accounts",
            headers: defaultHeader(),
        );
    }
}
