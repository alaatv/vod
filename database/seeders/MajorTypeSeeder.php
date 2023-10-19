<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('majortypes')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'highschool',
                'displayName' => 'دبیرستانی',
                'description' => 'رشته های دبیرستانی',
            ],
            [
                'id'          => '2',
                'name'        => 'university',
                'displayName' => 'دانشگاهی',
                'description' => 'رشته های دانشگاهی',
            ],
        ];

        DB::table('majortypes')
          ->insert($data); // Query Builder
    }
}
