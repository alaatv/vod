<?php

namespace Database\Seeders\Orders;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClosedAndPaidOrderWithCouponSeeder extends Seeder
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
                        'isInInstalment' => 0,
                        'customerDescription' => null,
                    ])
                    ->has(
                        Orderproduct::factory()
                            ->count(4)
                            ->state([
                                'cost' => 25000,
                                'tmp_final_cost' => 22500,
                            ])
                    )
                    ->has(Transaction::factory())
                    ->for(Coupon::factory())
            )
            ->count(10)
            ->create();
    }
}
