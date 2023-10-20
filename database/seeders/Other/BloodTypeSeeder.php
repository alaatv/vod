<?php

namespace Database\Seeders\Other;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BloodTypeSeeder extends Seeder
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
                'name' => 'O+',
                'displayName' => 'O مثبت',
            ],
            [
                'id' => '2',
                'name' => 'O−',
                'displayName' => 'O منفی',
            ],
            [
                'id' => '3',
                'name' => 'A+',
                'displayName' => 'A مثبت',
            ],
            [
                'id' => '4',
                'name' => 'A−',
                'displayName' => 'A منفی',
            ],
            [
                'id' => '5',
                'name' => 'B+',
                'displayName' => 'B مثبت',
            ],
            [
                'id' => '6',
                'name' => 'B−',
                'displayName' => 'B منفی',
            ],
            [
                'id' => '7',
                'name' => 'AB+',
                'displayName' => 'AB مثبت',
            ],
            [
                'id' => '8',
                'name' => 'AB−',
                'displayName' => 'AB منفی',
            ],

        ];

        DB::table('bloodtypes')
            ->insert($data); // Query Builder
    }
}
