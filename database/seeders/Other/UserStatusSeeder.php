<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatusSeeder extends Seeder
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
                'name'        => 'active',
                'displayName' => 'فعال',
                'description' => 'اکانت کاربری فعال است',
            ],
            [
                'id'          => '2',
                'name'        => 'inactive',
                'displayName' => 'غیر فعال',
                'description' => 'اکانت کاربری غیرفعال است',
            ],
        ];

        DB::table('userstatuses')
          ->insert($data); // Query Builder
    }
}
