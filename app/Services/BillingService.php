<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function fillBillingTable(?array $orderId = null, ?string $from = null, ?string $to = null, ?int $new = null)
    {
        if (is_null($new)) {
            $latestInsertedTransaction = 0;
        } else {
            $latestInsertedTransaction =
                DB::table('billing')->select('t_id')->orderByDesc('t_id')->whereNull('deleted_at')->take(1)->get()->first()?->t_id ?? 0;
        }

        $transactionShareTempQuery = DB::table('transactions')
            ->join('orders', function ($join) {
                $join->on('transactions.order_id', '=', 'orders.id')
                    ->whereNull('orders.deleted_at');
            })->join('orderproducts', function ($join) {
                $join->on('orderproducts.order_id', '=', 'orders.id')
                    ->whereNull('orderproducts.deleted_at')
                    ->whereIn('orderproducttype_id', [1]);
            })->leftJoin('coupons', function ($join) {
                $join->on('coupons.id', '=', 'orders.coupon_id')
                    ->whereNull('coupons.deleted_at');
            })->join('products', function ($join) {
                $join->on('orderproducts.product_id', '=', 'products.id');
            })
            ->whereNull('transactions.deleted_at')
            ->select(
                'transactions.id as t_id',
                'transactions.transactionstatus_id as t_status_id',
                'orders.user_id as u_id',
                'orderproducts.id as op_id',
                'orders.id as o_id',
                'orderproducts.product_id as p_id',
                'transactions.transactiongateway_id as gateway_id',
                'orderproducts.checkoutstatus_id as checkout_status_id',
                DB::raw('IF(orderproducts.includedInCoupon = 1,coupons.id,null) as coupon_id'),
                'products.financial_category_id as op_f_category_id',
                DB::raw('( ( (100 - orderproducts.discountPercentage ) / 100 ) * orderproducts.cost - orderproducts.discountAmount ) as op_cost'),
                DB::raw('IF(orderproducts.includedInCoupon = 1 and (coupons.discount is not null),((100 - coupons.discount) / 100 ) * ( ( (100 - orderproducts.discountPercentage ) / 100 ) * orderproducts.cost - orderproducts.discountAmount ) , ( ( (100 - orderproducts.discountPercentage ) / 100 ) * orderproducts.cost - orderproducts.discountAmount )) as op_final_cost'),
                DB::raw('IF( orderproducts.product_id IN ('.implode(',',
                        Product::DONATE_PRODUCT_ARRAY).') , 1  , 0 ) as is_donate'),
                DB::raw('IF( ABS(TIMESTAMPDIFF(MINUTE,transactions.completed_at,orders.completed_at)) <= 20,orders.completed_at,transactions.completed_at) t_completed_at'),
                //coupon amount
                'transactions.cost as t_cost',
            )
            ->where('transactions.transactionstatus_id', 3)
            ->where('transactions.paymentmethod_id', '<>', config('constants.PAYMENT_METHOD_WALLET'))
            ->where('transactions.id', '>', $latestInsertedTransaction)
            ->distinct();
        if (!is_null($orderId)) {
            $transactionShareTempQuery->whereIn('orders.id', $orderId);
        }

        //

        $transactionShareTempQuery = DB::query()->fromSub($transactionShareTempQuery, 't')->orderBy('t.t_completed_at');

        if (!is_null($from)) {
            $transactionShareTempQuery->where('t.t_completed_at', '>=', $from);
        }
        if (!is_null($to)) {
            $transactionShareTempQuery->where('t.t_completed_at', '<=', $to);
        }


        $noneDonateOrderProductsSum = DB::table('orderproducts')
            ->joinSub($transactionShareTempQuery, 'share', function ($join) {
                $join->on('orderproducts.id', '=', 'share.op_id');
            })
            ->whereNotIn('product_id', Product::DONATE_PRODUCT_ARRAY)
            ->select(
                'share.o_id',
                DB::raw(' SUM(share.op_final_cost) as sum_op_final_cost'),
            )
            ->groupBy('share.t_id', 'share.o_id')
            ->distinct();

        $donateOrderProductsSum = DB::table('orderproducts')
            ->joinSub($transactionShareTempQuery, 'share', function ($join) {
                $join->on('orderproducts.id', '=', 'share.op_id');
            })
            ->whereIn('product_id', Product::DONATE_PRODUCT_ARRAY)
            ->select(
                'share.o_id',
                DB::raw(' SUM(share.op_final_cost) as sum_op_final_cost'),
            )
            ->groupBy('share.t_id', 'share.o_id')
            ->distinct();

        DB::table('transactions')
            ->rightJoinSub($transactionShareTempQuery, 'share', function ($join) {
                $join->on('transactions.id', '=', 'share.t_id');
            })
            ->leftJoinSub($noneDonateOrderProductsSum, 'noneDonate', function ($join) {
                $join->on('share.o_id', '=', 'noneDonate.o_id');
            })
            ->leftJoinSub($donateOrderProductsSum, 'donate', function ($join) {
                $join->on('share.o_id', '=', 'donate.o_id');
            })
            ->select(
                'share.*',
                DB::raw('IF(isnull(noneDonate.sum_op_final_cost) =1 , 0 ,noneDonate.sum_op_final_cost ) as sum_none_donate_op_final_cost'),
                DB::raw('IF(isnull(donate.sum_op_final_cost)=1 , 0 ,donate.sum_op_final_cost ) as sum_donate_op_final_cost'),
                DB::raw('IF( share.is_donate = 1 , share.op_final_cost / donate.sum_op_final_cost  , share.op_final_cost / noneDonate.sum_op_final_cost ) as op_share_ratio'),
                DB::raw('now() as created_at'),
            )->distinct()->orderBy('share.t_id')->chunk(5000, function ($data) {
                foreach ($data->chunk(1000) as $chunkData) {
                    DB::table('billing')->insertOrIgnore(json_decode(json_encode($chunkData), true));
                }


            });
    }

    public function calculateOPshareAmountForBillingTable(
        ?int $orderId = null,
        ?string $from = null,
        ?string $to = null
    ) {
        $billings = $this->getBillingBuilder($orderId, $from, $to);
        $b1 = clone $billings;
        $b2 = clone $billings;
        $this->calcShareDoesNotHaveDonate($b1);
        $this->calcShareHaveDonate($b2);
    }

    public function getBillingBuilder(?int $orderId = null, ?string $from = null, ?string $to = null): Builder
    {
        $billings = DB::table('billing')
            ->whereNull('deleted_at')
            ->whereNull('op_share_amount');
        if (!is_null($orderId)) {
            $billings = $billings->where('o_id', $orderId);
        }
        if (!is_null($from)) {
            $billings = $billings->where('t_completed_at', '>=', $from);
        }
        if (!is_null($to)) {
            $billings = $billings->where('t_completed_at', '<=', $to);
        }
        $min = clone $billings;
        $donate = $billings->groupBy('o_id')
            ->select(
                'o_id',
                DB::raw('IF(SUM(is_donate) > 0, 1, 0) as has_donate'),
            );
        $min = $min->groupBy('t_id', 'o_id')
            ->select(
                't_id',
                'o_id',
                DB::raw('MIN(t_cost) as t_cost'),
            );
        $addSumT = DB::query()->fromSub($min, 'm')
            ->groupBy('m.o_id')
            ->select(
                'm.o_id',
                DB::raw('SUM(m.t_cost) as sum_t_cost'),
                DB::raw('COUNT(m.t_cost) as t_count'),
            );
        return DB::table('billing')
            ->rightJoinSub($donate, 'share', function ($join) {
                $join->on('billing.o_id', '=', 'share.o_id');
            })->rightJoinSub($addSumT, 't', function ($join) {
                $join->on('billing.o_id', '=', 't.o_id');
            })
            ->distinct();
    }

    private function calcShareDoesNotHaveDonate(Builder $billings)
    {
        $order = clone $billings;
        $order->select('billing.o_id')->distinct()->orderBy('billing.o_id')->chunk(100,
            function ($orders) use ($billings) {
                foreach ($orders->chunk(5000) as $chunkOrder) {
                    $b = clone $billings;
                    $bills = $b
                        ->where('has_donate', '=', 0)
                        ->whereIn('billing.o_id', json_decode(json_encode($chunkOrder), true))
                        ->select('billing.t_id', 'billing.op_id',
                            DB::raw('( billing.op_share_ratio * billing.t_cost ) as op_share_amount'))
                        ->orderBy('billing.t_id')->get();

                    foreach ($bills as $bill) {
                        DB::table('billing')
                            ->where('op_id', $bill->op_id)
                            ->where('t_id', $bill->t_id)
                            ->update([
                                'op_share_amount' => $bill->op_share_amount,
                            ]);
                    }
                }

            });
        return $billings;
    }

    private function calcShareHaveDonate(Builder $billings)
    {
        $b1 = clone $billings;
        $b2 = clone $billings;
        $this->calcShareHaveDonateWithOnlyOneTransaction($b1);
        $this->calcShareHaveDonateWithMultipleTransactions($b2);
    }

    private function calcShareHaveDonateWithOnlyOneTransaction(Builder $billings)
    {
        $order = clone $billings;
        $order->select('billing.o_id')->distinct()->orderBy('billing.o_id')->chunk(5000,
            function ($orders) use ($billings) {
                foreach ($orders->chunk(100) as $chunkOrder) {
                    $b = clone $billings;
                    $bills = $b->where('has_donate', '=', 1)
                        ->where('t_count', '=', 1)
                        ->whereIn('billing.o_id', json_decode(json_encode($chunkOrder), true))
                        ->select('billing.t_id',
                            'p_id',
                            'billing.op_id',
                            DB::raw('( IF( is_donate = 1 , (op_share_ratio * sum_donate_op_final_cost) , op_share_ratio * ( t_cost - sum_donate_op_final_cost) ) ) as op_share_amount')
                        )
                        ->orderBy('billing.t_id')
                        ->get();
                    foreach ($bills as $billing) {
                        DB::table('billing')
                            ->where('op_id', $billing->op_id)
                            ->where('t_id', $billing->t_id)
                            ->update([
                                'op_share_amount' => $billing->op_share_amount,
                            ]);
                    }

                }
            });
        return $billings;
    }

    private function calcShareHaveDonateWithMultipleTransactions(Builder $billings)
    {
        $order = clone $billings;
        $order->select('billing.o_id')->distinct()->orderBy('billing.o_id')->chunk(5000,
            function ($orders) use ($billings) {
                foreach ($orders->chunk(100) as $chunkOrder) {
                    $b = clone $billings;
                    $bills = $b->where('has_donate', '=', 1)
                        ->orderBy('billing.o_id')
                        ->where('t_count', '>', 1)
                        ->whereIn('billing.o_id', json_decode(json_encode($chunkOrder), true))
                        ->get();
                    $g = $bills->groupBy('o_id');
                    /** @var Collection $data */
                    foreach ($g as $oId => $data) {

                        $subG = $data->groupBy('is_donate');
                        $remainDonate = $data->first()->sum_donate_op_final_cost;
                        $sumTCost = $data->first()->sum_t_cost;
                        $transactions = collect();
                        $nonDonateData = $subG->get(0);
                        $remainT = $sumTCost - $remainDonate;
                        $transactionRatio = ($sumTCost == 0) ? 0 : $remainT / $sumTCost;

                        if (isset($nonDonateData)) {
                            foreach ($nonDonateData as $datum) {
                                $d = $datum->op_share_ratio * $datum->t_cost * $transactionRatio;
                                $t = $transactions->get($datum->t_id, 0);
                                $transactions->put($datum->t_id, $t + $d);

                                DB::table('billing')
                                    ->where('op_id', $datum->op_id)
                                    ->where('t_id', $datum->t_id)
                                    ->update([
                                        'op_share_amount' => $d,
                                    ]);
                            }
                        }

                        $donateData = $subG->get(1);
                        if (isset($donateData)) {
                            foreach ($donateData as $datum) {
                                $t = $transactions->get($datum->t_id, 0);
                                $d = $t >= 0 ? max(min($datum->t_cost - $t, $datum->op_final_cost), 0) : 0;
                                $transactions->put($datum->t_id, $t + $d);

                                DB::table('billing')
                                    ->where('op_id', $datum->op_id)
                                    ->where('t_id', $datum->t_id)
                                    ->update([
                                        'op_share_amount' => $d,
                                    ]);
                            }
                        }
                    }
                }
            });
        return $billings;
    }
}
