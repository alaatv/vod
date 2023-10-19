<?php

namespace Database\Seeders\Orders;

use App\Coupon;
use App\Order;
use App\Orderproduct;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;

class ClosedAndPaidOrderWithDiscountAndPartialCouponSeeder extends Seeder
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
                        'couponDiscount' => 10,
                        'cost' => 85500,
                        'costwithoutcoupon' => 95000,
                        'isInInstalment' => 0,
                        'customerDescription' => null,
                    ])
                    ->has(
                        Orderproduct::factory()
                            ->count(2)
                            ->state([
                                'cost' => 25000,
                                'tmp_final_cost' => 22500,
                                'discountPercentage' => 10,
                                'includedInCoupon' => 1,
                            ])
                            ->for(Product::factory()
                                ->has(Coupon::factory()
                                    ->state([
                                        'coupontype_id' => 2,
                                    ])
                                )
                            )
                    )
                    ->has(
                    Orderproduct::factory()
                        ->count(2)
                        ->state([
                            'cost' => 25000,
                            'tmp_final_cost' => 25000,
                        ])
                    )
                    ->has(Transaction::factory()
                        ->state([
                            'cost' => 85500,
                        ])
                    )
                    ->for(Coupon::factory()->state([
                        'coupontype_id' => 2,
                        'usageNumber' => 1
                    ]))
            )
            ->count(10)
            ->create();
    }
}
