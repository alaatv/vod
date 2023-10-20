<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionStatusSeeder extends Seeder
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
                'name'        => 'transferredToPay',
                'displayName' => 'ارجاع به بانک',
                'order'       => '7',
                'description' => 'ارجاع داده شده با بانک جهت پرداخت',
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
            [
                'id'          => '2',
                'name'        => 'unsuccessful',
                'displayName' => 'نا موفق',
                'order'       => '4',
                'description' => 'تراکنش بانکی ناموفق بوده است',
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
            [
                'id'          => '3',
                'name'        => 'successful',
                'displayName' => 'موفق',
                'order'       => '1',
                'description' => 'تراکنش بانکی موفق بوده است',
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
            [
                'id'          => '4',
                'name'        => 'pending',
                'displayName' => 'منتظر تایید',
                'order'       => '2',
                'description' => 'پرداخت انجام شده هنوز تایید نشده است',
                'created_at'  => '2017-06-01 00:00:00',
                'updated_at'  => '2017-06-01 00:00:00',
                'deleted_at'  => null,
            ],
            [
                'id'          => '5',
                'name'        => 'archivedSuccessful',
                'displayName' => 'موفق بایگانی شده',
                'order'       => '6',
                'description' => 'تراکنش موفقی که بایگانی شده است',
                'created_at'  => '2017-12-23 17:11:12',
                'updated_at'  => '2017-12-23 17:11:12',
                'deleted_at'  => null,
            ],
            [
                'id'          => '6',
                'name'        => 'unpaid',
                'displayName' => 'منتظر پرداخت',
                'order'       => '3',
                'description' => 'تراکنشی که قرار است در تاریخ معین شده پرداخت شود',
                'created_at'  => '2018-02-26 11:30:45',
                'updated_at'  => '2018-02-26 11:30:45',
                'deleted_at'  => null,
            ],
            [
                'id'          => '7',
                'name'        => 'suspended',
                'displayName' => 'معلق',
                'order'       => '5',
                'description' => 'تراکنش تایید شده ای که در حالت معلق قرار دارد',
                'created_at'  => '2018-05-31 16:39:16',
                'updated_at'  => '2018-05-31 16:39:16',
                'deleted_at'  => null,
            ],
        ];
        DB::table('transactionstatuses')
          ->insert($data); // Query Builder
    }
}
