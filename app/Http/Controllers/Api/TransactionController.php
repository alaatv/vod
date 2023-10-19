<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction as TransactionResource;
use App\Models\Bankaccount;
use App\Models\Bankaccount;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function withdraw(Request $request)
    {
        $user = $request->user();
        if ($user->hasPermission(config('constants.LIST_WITHDRAW_TRANSACTIONS'))) {
            $transactions = Transaction::settlement()->with('destinationBankAccount.user')->paginate();
            return TransactionResource::collection($transactions);
        }
        $bankAccount = Bankaccount::where('user_id', $user->id)->first();
        $transactions = Transaction::settlement($bankAccount->id)->paginate();
        return TransactionResource::collection($transactions);
    }
}
