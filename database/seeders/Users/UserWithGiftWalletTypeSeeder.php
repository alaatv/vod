<?php

namespace Database\Seeders\Users;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class UserWithGiftWalletTypeSeeder extends Seeder
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
                Wallet::factory()
                    ->state([
                        'balance' => 1000000,
                        'wallettype_id' => 2,
                    ])
                    ->has(
                        Transaction::factory()
                            ->state([
                                'order_id' => null,
                                'cost' => -1000000,
                                'transactionID' => null,
                                'paymentmethod_id' => null,
                                'device_id' => null,
                                'transactiongateway_id' => null,
                                'completed_at' => now('Asia/Tehran'),
                            ])
                    )
            )
            ->count(10)
            ->create();
    }
}
