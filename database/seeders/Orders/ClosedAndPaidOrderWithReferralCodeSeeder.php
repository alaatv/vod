<?php

namespace Database\Seeders\Orders;

use App\Models\ReferralCode;
use App\Models\ReferralRequest;
use App\Models\Order;
use App\Models\Orderproduct;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class ClosedAndPaidOrderWithReferralCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                Order::factory()
                    ->state([
                        'user_id' => 1,
                        'orderstatus_id' => 2,
                        'paymentstatus_id' => 3,
                        'coupon_id' => null,
                        'couponDiscount' => 0,
                        'cost' => 0,
                        'costwithoutcoupon' => 1000000,
                        'isInInstalment' => 0,
                        'customerDescription' => null,
                    ])
                    ->has(
                        Orderproduct::factory()
                            ->count(4)
                            ->state([
                                'cost' => 250000,
                                'tmp_final_cost' => 250000,
                            ])
                    )
                    ->has(Transaction::factory())
                    ->for(ReferralCode::factory()
                        ->for(ReferralRequest::factory()))
            ->count(10)
            ->create();
    }
}
