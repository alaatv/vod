<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SMSResultSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $table = 'sms_results';
        $data = [
            ['id' => '1','name' => 'done','display_name' => 'انجام شده','description' => 'پیامک بدون مشکل توسط api ارسال شده است و bulk_id دریافت شده است','created_at' => '2021-03-01 09:55:13','updated_at' => '2021-03-01 09:55:13'],
            ['id' => '2','name' => 'send_fail','display_name' => 'خطا در ارسال','description' => 'هیچ پاسخی از api ی ارسال پیامک دریافت نشده است','created_at' => '2021-03-01 09:55:13','updated_at' => '2021-03-01 09:55:13'],
            ['id' => '3','name' => 'response_error','display_name' => 'خطا در پاسخ','description' => 'درون پاسخ برگشتی از api خطا وجود دارد','created_at' => '2021-03-01 09:55:13','updated_at' => '2021-03-01 09:55:13'],
        ];
        DB::table($table)
            ->insert($data); // Query Builder
    }
}
