<?php

namespace Database\Seeders\Users;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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
