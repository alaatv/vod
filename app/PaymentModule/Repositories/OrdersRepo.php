<?php

namespace App\PaymentModule\Repositories;

use App\Models\Order;
use Carbon\Carbon;

class OrdersRepo
{
    public static function closeOrder(int $id, array $parameters = [])
    {
        $data = [
            'orderstatus_id' => config('constants.ORDER_STATUS_CLOSED'),
            'completed_at' => Carbon::createFromFormat('Y-m-d H:i:s', Carbon::now())->timezone('Asia/Tehran'),
        ];
        $data = array_merge($data, $parameters);

        return Order::where('id', $id)
            ->update($data);
    }
}
