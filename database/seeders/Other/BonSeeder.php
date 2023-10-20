<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonSeeder extends Seeder
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
                'name'        => 'alaa',
                'displayName' => 'فرات بن',
                'description' => 'بن تخفیف فرات',
            ],
        ];

        DB::table('bons')
          ->insert($data); // Query Builder
    }
}
