<?php

namespace Database\Seeders\Orders;

use App\Order;
use App\Orderproduct;
use App\Transaction;
use App\User;
use App\Wallet;
use Illuminate\Database\Seeder;

class ClosedWalletAndOnlinePaidOrderSeeder extends Seeder
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
                        'cost' => 50000,
                    ])
            )
            ->create();
        $wallet->update([
            'balance' => 0,
        ]);
    }
}
