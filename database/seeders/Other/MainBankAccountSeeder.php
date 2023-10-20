<?php

namespace Database\Seeders\Other;

use App\Models\Bankaccount;
use Illuminate\Database\Seeder;

class MainBankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bankaccount::factory()->state([
            'id' => 1,
            'user_id' => 1,
            'bank_id' => 1,
        ])->create();
    }
}
