<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationalBaseSeeder extends Seeder
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
                'title' => 'متوسطه اول',
            ],
            [
                'id' => '2',
                'title' => 'متوسطه دوم',
            ],
        ];

        DB::table('educationalBases')->insert($data); // Query Builder
    }
}
