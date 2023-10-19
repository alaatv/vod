<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderproductInterrelationSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('orderproductinterrelations')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'parent-child',
                'displayName' => 'فرزند-والد',
                'description' => 'به طوری قرارداری اولی والد دومی می باشد',
            ],
        ];

        DB::table('orderproductinterrelations')
          ->insert($data); // Query Builder
    }
}
