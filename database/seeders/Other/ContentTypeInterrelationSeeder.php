<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentTypeInterrelationSeeder extends Seeder
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
                'name'        => 'parent-child',
                'displayName' => 'فرزند-والد',
                'description' => 'به طوری قرارداری اولی والد دومی می باشد',
            ],
        ];

        DB::table('contenttypeinterrelations')
          ->insert($data); // Query Builder
    }
}
