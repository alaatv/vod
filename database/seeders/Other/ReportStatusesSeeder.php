<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportStatusesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'title' => 'creating',
                'title_display_name' => 'در حال تولید',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'created',
                'title_display_name' => 'تولید شده',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'title' => 'failed',
                'title_display_name' => 'خطا در ایجاد فایل',
                'created_at' => now(),
            ],
        ];
        DB::table('report_statuses')
            ->insert($data);
    }
}
