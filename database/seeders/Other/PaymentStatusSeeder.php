<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusSeeder extends Seeder
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
                'displayName' => 'پرداخت نشده',
                'description' => 'هیچ مبلغی پرداخت نشده است',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '2',
                'name' => 'indebted',
                'displayName' => 'پرداخت قسطی',
                'description' => 'بخشی از مبلغ پرداخت شده است',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '3',
                'name' => 'paid',
                'displayName' => 'پرداخت شده',
                'description' => 'تمام مبلغ پرداخت شده است',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
        ];

        DB::table('paymentstatuses')
            ->insert($data); // Query Builder
    }
}
