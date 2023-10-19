<?php

namespace Database\Seeders\Orders;

use App\Coupon;
use App\Order;
use App\Orderproduct;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;

class ClosedAndPaidOrderWithPercentageDiscountSeeder extends Seeder
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
                        'cost' => 90000,
                        'costwithoutcoupon' => 100000,
                        'isInInstalment' => 0,
                        'customerDescription' => null,
                    ])
                    ->has(
                        Orderproduct::factory()
                            ->count(4)
                            ->state([
                                'cost' => 25000,
                                'tmp_final_cost' => 22500,
                                'discountPercentage' => 10,
                                'includedInCoupon' => 1,
                            ])
                            ->for(Product::factory()
                                ->has(Coupon::factory())
                            )
                    )
                    ->has(Transaction::factory()
                        ->state([
                            'cost' => 90000,
                        ])
                    )
            )
            ->count(10)
            ->create();
    }
}
