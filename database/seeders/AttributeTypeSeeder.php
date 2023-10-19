<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attributetypes')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'main',
                'description' => 'صفت اصلی',
            ],
            [
                'id'          => '2',
                'name'        => 'extra',
                'description' => 'صفت غیر اصلی',
            ],
            [
                'id'          => '3',
                'name'        => 'information',
                'description' => 'صفت توضیحی',
            ],
        ];

        DB::table('attributetypes')
          ->insert($data); // Query Builder
    }
}
