<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebsitePageSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        /*
        $data = [
            [
                'url'         => '/home',
                'displayName' => 'خانه',
            ],
            [
                'url'         => '/لیست-مقالات',
                'displayName' => 'مقالات',
            ],
        ];
        DB::table('websitepages')
          ->insert($data); // Query Builder*/

        $data = [
            [
                'url' => '/shop',
                'displayName' => 'صفحه اصلی فروشگاه',
            ],
        ];
        DB::table('websitepages')
            ->insert($data); // Query Builder
    }
}
