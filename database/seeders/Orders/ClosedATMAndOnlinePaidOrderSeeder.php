<?php

namespace Database\Seeders\Orders;

use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClosedATMAndOnlinePaidOrderSeeder extends Seeder
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
                        'paymentstatus_id' => 3,
                        'coupon_id' => null,
                        'couponDiscount' => 0,
                        'cost' => 100000,
                        'costwithoutcoupon' => 100000,
                        'isInInstalment' => 0,
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
                                'transactionID' => null,
                                'paymentmethod_id' => 2,
                                'cost' => 50000,
                                'device_id' => null,
                                'transactiongateway_id' => null,
                                'referenceNumber' => random_int(100000000, 999999999),
                                'completed_at' => now()->addHours(5),
                            ])
                    )
                    ->has(
                        Transaction::factory()
                            ->state([
                                'cost' => 50000,
                            ])
                    )
            )
            ->count(10)
            ->create();
    }
}
