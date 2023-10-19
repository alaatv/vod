<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserBonStatusSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('userbonstatuses')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'active',
                'displayName' => 'فعال',
                'description' => 'کاربر از بن استفاده نکرده و آماده استفاده است',
                'order'       => '0',
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
            [
                'id'          => '2',
                'name'        => 'expired',
                'displayName' => 'باطل شده',
                'description' => 'بن کاربر قبل از استفاده غیر فعال(باطل) شده است ',
                'order'       => '0',
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
            [
                'id'          => '3',
                'name'        => 'used',
                'displayName' => 'استفاده کرده',
                'description' => 'کاربر از بن خود با موفقیت استفاده کرده است',
                'order'       => '0',
                'created_at'  => null,
                'updated_at'  => null,
                'deleted_at'  => null,
            ],
        ];

        DB::table('userbonstatuses')
          ->insert($data); // Query Builder
    }
}
