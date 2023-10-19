<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OnlineTransactionGatewaysSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('onlinetransactiongateways')
          ->delete();
        $data = [
            [
                'id'             => '1',
                'name'           => 'zarinpal',
                'displayName'    => 'زرین پال',
                'description'    => 'درگاه پرداخت الکترونیک زرین پال',
                'merchantNumber' => 'c46a34de-c82c-11e5-8943-000c295eb8fc',
            ],
        ];

        DB::table('onlinetransactiongateways')
          ->insert($data); // Query Builder
    }
}
