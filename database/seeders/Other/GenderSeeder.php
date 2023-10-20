<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
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
                'name'        => 'آقا',
                'description' => 'جنست مذکر',
            ],
            [
                'id'          => '2',
                'name'        => 'خانم',
                'description' => 'جنسست مونث',
            ],
        ];

        DB::table('genders')
          ->insert($data); // Query Builder
    }
}
