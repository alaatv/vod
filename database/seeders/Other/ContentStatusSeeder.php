<?php

namespace Database\Seeders\Other;

use App\Models\ContentsStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contents_statuses')->insert([
            [
                'id' => ContentsStatus::CONTENT_STATUS_PENDING,
                'name' => 'pending',
                'display_name' => 'در انتظار',
                'description' => 'در انتظار ساخت کیفیت ها برای محتوا ها'
            ],
            [
                'id' => ContentsStatus::CONTENT_STATUS_DRAFT,
                'name' => 'draft',
                'display_name' => 'پیش نویس',
                'description' => 'در انتظار درج مشخصات'
            ],
            [
                'id' => ContentsStatus::CONTENT_STATUS_COMPLETED,
                'name' => 'completed',
                'display_name' => 'تکمیل شده',
                'description' => 'ساخت محتوا تکمیل شد'
            ]
        ]);
    }
}
