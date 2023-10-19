<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventresultStatusSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('eventresultstatuses')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'unseen',
                'displayName' => 'دیده نشده',
                'description' => 'هنوز دیده نشده',
            ],
            [
                'id'          => '2',
                'name'        => 'published',
                'displayName' => 'منتشر شده',
                'description' => 'این نتیجه منتشر شده است',
            ],
            [
                'id'          => '3',
                'name'        => 'unpublishable',
                'displayName' => 'منتشر نشود',
                'description' => 'نامناسب برای انتشار',
            ],
        ];

        DB::table('eventresultstatuses')
          ->insert($data); // Query Builder
    }
}
