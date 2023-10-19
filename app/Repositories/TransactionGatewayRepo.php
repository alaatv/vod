<?php

namespace App\Repositories;

use App\Models\Transactiongateway;
use Illuminate\Database\Eloquent\Builder;

class TransactionGatewayRepo
{
    public static function getTransactionGatewayByName($name)
    {
        return nullable(Transactiongateway::where('name', $name)->first());
    }

    /**
     * @param  array  $filters
     *
     * @return Transactiongateway|Builder
     */
    public static function getTransactionGateways(array $filters = [])
    {
        $gateways = Transactiongateway::query();
        self::filter($filters, $gateways);
        return $gateways;
    }

    /**
     * @param  array  $filters
     * @param       $transactions
     */
    private static function filter(array $filters, Builder $transactions): void
    {
        foreach ($filters as $key => $filter) {
            $transactions->where($key, $filter);
        }
    }

    public static function findEnabledGateways()
    {
        $gatewaysOptions = Transactiongateway::query();
        if (!isDevelopmentMode()) {
            $gatewaysOptions = $gatewaysOptions->enable(true);
        }

        return $gatewaysOptions;
    }

    /**
     * @return Transactiongateway|Builder|\Illuminate\Database\Query\Builder
     */
    public static function getRandomGateway()
    {
        return Transactiongateway::query()->enable(true)
            ->inRandomOrder()
            ->limit(1);
    }
}
