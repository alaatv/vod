<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WallettypeSeeder extends Seeder
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
                'name'        => 'main',
                'displayName' => 'اصلی',
                'description' => 'کیف پول اصلی کاربر',
            ],
            [
                'id'          => '2',
                'name'        => 'gift',
                'displayName' => 'هدیه',
                'description' => 'کیف پول هدیه کاربر',
            ],
        ];

        DB::table('wallettypes')
          ->insert($data); // Query Builder
    }
}
