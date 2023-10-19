<?php

namespace Database\Seeders\Orders;

use App\Order;
use App\Orderproduct;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;

class ClosedOnePaidOrderWithInstalment extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->has(
                Order::factory()
                    ->state([
                        'orderstatus_id' => 2,
                        'paymentstatus_id' => 2,
                        'coupon_id' => null,
                        'couponDiscount' => 0,
                        'cost' => 100000,
                        'costwithoutcoupon' => 100000,
                        'isInInstalment' => 1,
                        'customerDescription' => null,
                    ])
                    ->has(
                        Orderproduct::factory()
                            ->count(4)
                            ->state([
                                'cost' => 25000,
                                'tmp_final_cost' => 25000,
                            ])
                    )
                    ->has(
                        Transaction::factory()
                            ->state([
                                'cost' => 25000,
                            ])
                    )
                    ->has(
                        Transaction::factory()
                            ->state([
                                'cost' => 25000,
                                'transactionID' => null,
                                'paymentmethod_id' => null,
                                'device_id' => null,
                                'transactiongateway_id' => null,
                                'transactionstatus_id' => 6,
                                'completed_at' => null,
                            ])
                            ->count(3)
                    )
            )
            ->count(10)
            ->create();
    }
}
