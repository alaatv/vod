<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionsSerivce
{
    /**
     * محاسبه ی اقساط ایتم های یک سفارش
     * @param  Collection|array  $order_products
     * @return Collection
     */
    public static function calculateInstalments(Collection|array $order_products): ?Collection
    {
        $order_products_instalments = collect($order_products)->pick(['tmp_final_cost', 'instalmentQty']);
        if ($order_products_instalments->whereNull('instalmentQty')->isNotEmpty()) {
            return null;
        }
        $max_instalment_qty = count($order_products_instalments->max('instalmentQty'));

        $transactions_price = collect();
        for ($i = 0; $i < $max_instalment_qty; $i++) {
            $transactions_price->push(new Transaction());
            $order_products_instalments->map(function ($product) use ($i, $max_instalment_qty, &$transactions_price) {
                if ($i < count($product['instalmentQty'])) {
                    $transactions_price[$i]['cost'] += (int) (($product['tmp_final_cost'] * $product['instalmentQty'][$i]) / 100);
                }
            });
            $transactions_price[$i]['description'] = 'قسط شماره '.$i + 1;
            $transactions_price[$i]['destinationBankAccount_id'] = 1;
            $transactions_price[$i]['paymentmethod_id'] = config('constants.PAYMENT_METHOD_ONLINE');
            $transactions_price[$i]['transactionstatus_id'] = config('constants.TRANSACTION_STATUS_UNPAID');
            $transactions_price[$i]['deadline_at'] = Carbon::now('Asia/Tehran')->addMonths($i);
        }
        return $transactions_price;
    }
}
