<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionInterrelationSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            [
                'id'          => '1',
                'name'        => 'parent-child',
                'displayName' => 'فرزند-والد',
                'description' => 'به طوری قرارداری اولی والد دومی می باشد',
            ],
        ];
        DB::table('transactioninterraltions')
          ->insert($data); // Query Builder
    }
}
