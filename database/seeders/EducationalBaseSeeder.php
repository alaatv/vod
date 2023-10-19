<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EducationalBaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('educationalBases')->delete();
        $data = [
            [
                'id'             => '1',
                'title'          => 'متوسطه اول',
            ],
            [
                'id'             => '2',
                'title'          => 'متوسطه دوم',
            ],
        ];

        DB::table('educationalBases')->insert($data); // Query Builder
    }
}
