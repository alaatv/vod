<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonsOfLockedOrderproductsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reasonsOfLockedOrderproducts')->delete();
        $data = [
            [
                'id' => '1',
                'text' => 'این محصولات با تغییرات ساختار آموزشی قابل استفاده نیست',
            ],
            [
                'id' => '2',
                'text' => 'هر کاربر فقط یکبار می تواند از بورسیه آلاء استفاده کند',
            ],
            [
                'id' => '3',
                'text' => 'هدایای آلاء پس از زمان استفاده منقضی می شود',
            ],
        ];

        DB::table('reasonsOfLockedOrderproducts')->insert($data); // Query Builder
    }
}
