<?php

namespace Database\Seeders\Orders;

use App\Models\ReferralCode;
use App\Models\ReferralRequest;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class ClosedAndPaidOrderWithNoneProfitableReferralCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReferralRequest::factory()->has(
            ReferralCode::factory()->state([
                'isAssigned' => 1,
                'usageNumber' => 1,
            ])->has(
                Order::factory()
                    ->state([
                        'user_id' => 10,
                        'orderstatus_id' => 2,
                        'paymentstatus_id' => 3,
                        'coupon_id' => null,
                        'couponDiscount' => 0,
                        'cost' => 0,
                        'costwithoutcoupon' => 10000,
                        'isInInstalment' => 0,
                        'customerDescription' => null,
                    ])
                    ->has(
                        Orderproduct::factory()
                            ->count(4)
                            ->state([
                                'cost' => 2500,
                                'tmp_final_cost' => 2500,
                            ])
                    )
                    ->has(Transaction::factory()->state([
                        'cost' => 10000,
                    ]))
            )->count(10)
        )->create();
    }
}
