<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReportStatusesSeeder extends AlaaSeeder
{
    protected function setData(): void
    {
        $this->data = [
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
    }

    protected function setTable(): void
    {
        $this->table = 'report_statuses';
    }
}
