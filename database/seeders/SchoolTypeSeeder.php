<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class SchoolTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schoolTypes')->delete();
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
