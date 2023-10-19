<?php

namespace Database\Seeders\Orders;

use App\Models\Orderfile;
use App\Models\Ordermanagercomment;
use App\Order;
use App\Orderfile;
use App\Ordermanagercomment;
use App\Orderproduct;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;

class ClosedAndPaidOrderWithManagerWithManagerComments extends Seeder
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
                    ->has(Ordermanagercomment::factory()->count(4))
                    ->hasfiles(Orderfile::factory())
                    ->has(Transaction::factory())
            )
            ->count(10)
            ->createQuietly();
    }
}
