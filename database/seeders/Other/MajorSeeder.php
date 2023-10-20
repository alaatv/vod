<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorSeeder extends Seeder
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
                'name'        => 'ریاضی',
                'description' => 'رشته ریاضی مقطع متوسطه',
            ],
            [
                'id'          => '2',
                'name'        => 'تجربی',
                'description' => 'رشته تجربی مقطع متوسطه',
            ],
            [
                'id'          => '3',
                'name'        => 'انسانی',
                'description' => 'رشته انسانی مقطع متوسطه',
            ],
            [
                'id'          => '4',
                'name'        => 'علوم و معارف اسلامی',
                'description' => 'علوم_و_معارف_اسلامی',
            ],
            [
                'id'          => '5',
                'name'        => 'زبان',
                'description' => 'زبان',
            ],
            [
                'id'          => '6',
                'name'        => 'هنر',
                'description' => 'هنر',
            ],
        ];

        DB::table('majors')
          ->insert($data); // Query Builder
    }
}
