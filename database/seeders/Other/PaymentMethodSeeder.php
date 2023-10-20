<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
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
                'name' => 'online',
                'displayName' => 'آنلاین',
                'description' => 'پرداخت به روش آنلاین',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '2',
                'name' => 'ATM',
                'displayName' => 'عابر بانک',
                'description' => 'پرداخت از طریق عابر بانک',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '3',
                'name' => 'POS',
                'displayName' => 'کارت خوان',
                'description' => 'پرداخت از طریق کارت خوان',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => '4',
                'name' => 'paycheck',
                'displayName' => 'چک بانکی',
                'description' => 'پرداخت با چک بانکی',
                'created_at' => '2017-02-23 00:00:00',
                'updated_at' => '2017-02-23 00:00:00',
                'deleted_at' => null,
            ],
            [
                'id' => '5',
                'name' => 'wallet',
                'displayName' => 'کیف پول',
                'description' => 'پرداخت از طریق اعتبار کیف پول',
                'created_at' => '2018-05-17 14:45:29',
                'updated_at' => '2018-05-17 14:45:29',
                'deleted_at' => null,
            ],
        ];

        DB::table('paymentmethods')
            ->insert($data); // Query Builder
    }
}
