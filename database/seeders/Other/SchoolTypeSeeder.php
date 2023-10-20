<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolTypeSeeder extends Seeder
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
                'id'             => '1',
                'title'          => 'دولتی',
            ],
            [
                'id'             => '2',
                'title'          => 'نمونه دولتی',
            ],
            [
                'id'             => '3',
                'title'          => 'هیات امنایی',
            ],
            [
                'id'             => '4',
                'title'          => 'غیر دولتی',
            ],
            [
                'id'             => '5',
                'title'          => 'سمپاد',
            ],
            [
                'id'             => '6',
                'title'          => 'شاهد',
            ],
        ];

        DB::table('schoolTypes')->insert($data); // Query Builder
    }
}
