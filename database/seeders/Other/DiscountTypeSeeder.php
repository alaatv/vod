<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountTypeSeeder extends Seeder
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
                'name'        => 'percentage',
                'displayName' => 'درصد',
                'description' => 'تخفیف درصدی می باشد',
            ],
            [
                'id'          => '2',
                'name'        => 'cost',
                'displayName' => 'مبلغ',
                'description' => 'تخفیف به صورت مبلغی',
            ],
        ];

        DB::table('discounttypes')
          ->insert($data); // Query Builder
    }
}
