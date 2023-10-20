<?php

namespace Database\Seeders\Other;

use App\Models\BlockType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BannersBlockSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        if (!BlockType::find(7)) {
            return null;
        }

        $data = [
            [
                'title' => 'بالای لیست-نمای موبایل و تبلت',
                'type' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'بالای لیست-نمای دسکتاپ',
                'type' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'سمت چپ صفحه پایین لیست کانتنت های مشابه',
                'type' => 7,
                'created_at' => Carbon::now(),
            ],
            [
                'title' => 'سمت راست صفحه بالای توضیحات کانتنت',
                'type' => 7,
                'created_at' => Carbon::now(),
            ],
        ];

        DB::table('blocks')
            ->insert($data); // Query Builder
    }
}
