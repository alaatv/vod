<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContacttypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contacttypes')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'simple',
                'displayName' => 'ساده',
                'description' => 'دفترچه تلفن از ساده است',
            ],

            [
                'id'          => '2',
                'name'        => 'emergency',
                'displayName' => 'اضطراری',
                'description' => 'دفترچه تلفن از اضطراری است',
            ],
        ];

        DB::table('contacttypes')
          ->insert($data); // Query Builder
    }
}
