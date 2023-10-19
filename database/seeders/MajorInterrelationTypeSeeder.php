<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorInterrelationTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('majorinterrelationtypes')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'parent-child',
                'displayName' => 'فرزند-والد',
                'description' => 'به طوری قرارداری اولی والد دومی می باشد',
            ],
            [
                'id'          => '2',
                'name'        => 'accessible',
                'displayName' => 'مجاز بودن',
                'description' => 'رشته دوم برای رشته اول مجاز است',
            ],
        ];

        DB::table('majorinterrelationtypes')
          ->insert($data); // Query Builder
    }
}
