<?php


namespace App\Repositories;


use App\Models\Bankaccount;
use App\Models\Bankaccount;
use Illuminate\Database\Eloquent\Builder;

class BankaccountRepo
{
    /**
     * @param  array  $parameters
     *
     * @return Bankaccount
     */
    public static function firstOrCreateBankAccount(array $parameters): Bankaccount
    {
        $bankAccount = self::findBankAccount($parameters)->get();
        if ($bankAccount->isEmpty()) {
            return Bankaccount::create($parameters);
        }

        return $bankAccount->first();
    }

    /**
     * @param  array  $parameters
     *
     * @return Bankaccount|Builder
     */
    public static function findBankAccount(array $parameters)
    {
        $bankAccount = Bankaccount::query();
        foreach ($parameters as $key => $parameter) {
            $bankAccount->where($key, $parameter);
        }

        return $bankAccount;
    }
}
