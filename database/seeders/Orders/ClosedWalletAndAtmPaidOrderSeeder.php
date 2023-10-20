<?php

namespace Database\Seeders\Orders;

use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class ClosedWalletAndAtmPaidOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->state([
            'user_id' => $user->id,
            'wallettype_id' => 2,
            'balance' => 50000,
        ])->create();
        Transaction::factory()
            ->state([
                'order_id' => null,
                'wallet_id' => $wallet->id,
                'cost' => -50000,
                'transactionID' => null,
                'paymentmethod_id' => null,
                'device_id' => null,
                'transactiongateway_id' => null,
                'completed_at' => now('Asia/Tehran'),
            ])
            ->create();
        Order::factory()
            ->state([
                'user_id' => $user->id,
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
                        'wallet_id' => $wallet->id,
                        'cost' => 50000,
                        'transactionID' => null,
                        'paymentmethod_id' => 5,
                        'device_id' => null,
                        'transactiongateway_id' => null,
                        'transactionstatus_id' => 3,
                        'completed_at' => now(),
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
            ->create();
        $wallet->update([
            'balance' => 0,
        ]);
    }
}
