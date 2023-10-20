<?php

namespace Database\Seeders\Other;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhoneNumberProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $data = [
            [
                'id' => '1',
                'title' => 'Hamrahe aval',
                'display_name' => 'همراه اول',
                'pattern' => '//',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => '2',
                'title' => 'Irancell',
                'display_name' => 'ایرانسل',
                'pattern' => '//',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => '3',
                'title' => 'Rightel',
                'display_name' => 'رایتل',
                'pattern' => '/092[0-2][0-9]{7}/',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => '4',
                'title' => 'Shatel',
                'display_name' => 'شاتل',
                'pattern' => '/09981[0-4][0-9]{5}/',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => '5',
                'title' => 'Talia',
                'display_name' => 'تالیا',
                'pattern' => '/0932[0-9]{7}/',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('phone_number_providers')
            ->insert($data); // Query Builder
    }
}
