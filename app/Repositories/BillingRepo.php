<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class BillingRepo
{
    public static function getInvoiceByDate(
        ?string $from = null,
        ?string $to = null,
        ?array $gatewayId,
        ?array $productIds = null
    ) {
        $gatewayId = is_array($gatewayId) ? $gatewayId : [$gatewayId];
        $from = Carbon::parse($from);
        $to = Carbon::parse($to)->addDay()->subMicrosecond();

        $billings = self::getBillingBuilder(null, $from, $to, null, $gatewayId, $productIds)->orderBy('billing.o_id');

        $sum = clone $billings;
        $sum = $sum->groupBy('op_f_category_id')
            ->select(
                'op_f_category_id',
                DB::raw('SUM(op_share_amount) as sum_f_category_cost'),
            );
//        dump($sum->get());
        $count = clone $billings;
        $count = $count->groupBy('op_id', 'op_f_category_id')
            ->select(
                'op_id',
                'op_f_category_id',
                DB::raw('1 as op_count'),
            );
//        dump($count->get());
        $countOPInFCategory = DB::query()->fromSub($count, 'm')
            ->groupBy('m.op_f_category_id')
            ->select(
                'm.op_f_category_id',
                DB::raw('SUM(m.op_count) as op_count'),
            );

//        dump($countOPInFCategory->get());
        $merge = DB::query()->fromSub($countOPInFCategory, 'b')
            ->joinSub($sum, 's', function ($join) {
                $join->on('s.op_f_category_id', '=', 'b.op_f_category_id');
            })->select(
                'b.op_f_category_id',
                'b.op_count',
                's.sum_f_category_cost'
            );
//        dd($merge->get());
        return $merge->get();
    }

    private static function getBillingBuilder(
        ?array $orderId = null,
        ?string $from = null,
        ?string $to = null,
        ?array $ops = null,
        ?array $gateWayId = null,
        ?array $productIds = null
    ): Builder {
        $billings = DB::table('billing')->whereNull('deleted_at');

        if (!is_null($orderId) && !empty($orderId)) {
            $billings->whereIn('o_id', $orderId);
        }
        if (!is_null($ops) && !empty($ops)) {
            $billings->whereIn('op_id', $ops);
        }
        if (!is_null($from)) {
            $billings->where('t_completed_at', '>=', $from);
        }
        if (!is_null($to)) {
            $billings->where('t_completed_at', '<=', $to);
        }
        if (!is_null($gateWayId) && !empty($gateWayId)) {
            $billings->whereIn('gateway_id', $gateWayId);
        }
        if (!is_null($productIds) && !empty($productIds)) {
            $billings->whereIn('p_id', $productIds);
        }
        return (clone $billings);
    }
}
