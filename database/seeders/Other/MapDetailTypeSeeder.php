<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapDetailTypeSeeder extends Seeder
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
                'id' => '1',
                'title' => 'marker',
            ],
            [
                'id' => '2',
                'title' => 'polyline',
            ],
        ];

        DB::table('mapDetailTypes')->insert($data); // Query Builder
    }
}
