<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponTypeSeeder extends Seeder
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
                'id' => '1',
                'displayName' => 'کلی',
                'name' => 'overall',
                'description' => 'کپن برای همه محصولات سبد',
            ],
            [
                'id' => '2',
                'displayName' => 'جزئی',
                'name' => 'partial',
                'description' => 'کپن برای بعضی از محصولات سبد',
            ],
        ];
        DB::table('coupontypes')
            ->insert($data); // Query Builder
    }
}
