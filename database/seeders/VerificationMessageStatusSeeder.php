<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerificationMessageStatusSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('verificationmessagestatuses')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'sent',
                'displayName' => 'ارسال شده',
                'description' => 'پیام حاوی کد به کاربر ارسال شده ا ست',
            ],
            [
                'id'          => '2',
                'name'        => 'successful',
                'displayName' => 'موفق',
                'description' => 'اکانت کاربر با موفقیت توسط این کد تایید شد',
            ],
            [
                'id'          => '3',
                'name'        => 'notDelivered',
                'displayName' => 'نرسیده',
                'description' => 'پیام به دست کاربر نرسیده است',
            ],
            [
                'id'          => '4',
                'name'        => 'expired',
                'displayName' => 'منقضی شده',
                'description' => 'از تاریخ استفاده کد این پیام گذشته است',
            ],
        ];

        DB::table('verificationmessagestatuses')
          ->insert($data); // Query Builder
    }
}
