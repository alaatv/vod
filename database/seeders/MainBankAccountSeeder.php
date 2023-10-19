<?php

namespace Database\Seeders;

use App\Bankaccount;
use App\Models\Bankaccount;
use Illuminate\Database\Seeder;
use Schema;

class MainBankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Bankaccount::truncate();
        Bankaccount::factory()->state([
            'id' => 1,
            'user_id' => 1,
            'bank_id' => 1,
        ])->create();
        Schema::enableForeignKeyConstraints();
    }
}
