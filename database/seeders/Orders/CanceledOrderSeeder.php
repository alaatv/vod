<?php

namespace Database\Seeders\Orders;

use App\Order;
use App\Orderproduct;
use App\User;
use Illuminate\Database\Seeder;

class CanceledOrderSeeder extends Seeder
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
                        'orderstatus_id' => 3,
                        'paymentstatus_id' => 1,
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
            )
            ->count(10)
            ->create();
    }
}
