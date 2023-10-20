<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelativeSeeder extends Seeder
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
                'name' => 'father',
                'displayName' => 'پدر',
                'description' => 'پدر کاربر',
            ],
            [
                'id' => '2',
                'name' => 'mother',
                'displayName' => 'مادر',
                'description' => 'مادر کاربر',
            ],
        ];
        DB::table('relatives')
            ->insert($data); // Query Builder
    }
}
