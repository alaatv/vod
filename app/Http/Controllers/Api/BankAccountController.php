<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiBankAccountRequest;
use App\Services\BankAccountService;

class BankAccountController extends Controller
{
    public function index(BankAccountService $bankAccountService)
    {
        $bankAccounts = $bankAccountService->getUserBankAccounts(auth()->id());
        return response()->json($bankAccounts['data']);
    }

    public function store(ApiBankAccountRequest $request, BankAccountService $bankAccountService)
    {
        $bankAccount = $bankAccountService->store(
            auth()->id(),
            $request->input('preShabaNumber').$request->input('shabaNumber'),
            $request->get('cardNumber')
        );
        return response()->json($bankAccount, $bankAccount['status_code']);
    }
}
