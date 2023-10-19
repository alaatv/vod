<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MapDetailTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mapDetailTypes')->delete();
        $data = [
            [
                'id'          => '1',
                'title'       => 'marker',
            ],
            [
                'id'          => '2',
                'title'       => 'polyline',
            ],
        ];

        DB::table('mapDetailTypes')->insert($data); // Query Builder
    }
}
