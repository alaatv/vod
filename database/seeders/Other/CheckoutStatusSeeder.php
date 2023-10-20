<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckoutStatusSeeder extends Seeder
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
                'name' => 'unpaid',
                'displayName' => 'تسویه نشده',
                'description' => 'تسویه نشده است',
            ],
            [
                'id' => '2',
                'name' => 'paid',
                'displayName' => 'تسویه شده',
                'description' => 'تسویه شده است',
            ],

        ];

        DB::table('checkoutstatuses')
            ->insert($data); // Query Builder
    }
}
