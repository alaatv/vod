<?php

namespace Database\Seeders\Users;

use App\Transaction;
use App\User;
use App\Wallet;
use Illuminate\Database\Seeder;
use Schema;

class AllUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Wallet::truncate();
        Transaction::truncate();
        $this->call([
            UserSeeder::class,
            UserWithGiftWalletTypeSeeder::class,
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
