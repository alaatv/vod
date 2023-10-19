<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use src\Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ContentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contenttypes')
          ->delete();
        $data = [
            [
                'id'          => '1',
                'name'        => 'pamphlet',
                'displayName' => 'جزوه',
                'description' => 'جزوه',
                'order'       => '1',
                'enable'      => '1',
                'created_at'  => '2017-10-12 10:26:05',
                'updated_at'  => '2017-10-12 10:26:05',
                'deleted_at'  => null,
            ],
            [
                'id'          => '2',
                'name'        => 'exam',
                'displayName' => 'آزمون',
                'description' => 'آزمون',
                'order'       => '2',
                'enable'      => '1',
                'created_at'  => '2017-10-12 10:26:05',
                'updated_at'  => '2017-10-12 10:26:05',
                'deleted_at'  => null,
            ],
            [
                'id'          => '3',
                'name'        => 'ghalamchi',
                'displayName' => 'قلم چی',
                'description' => 'قلم_چی',
                'order'       => '1',
                'enable'      => '1',
                'created_at'  => '2017-10-12 10:26:05',
                'updated_at'  => '2017-10-12 10:26:05',
                'deleted_at'  => null,
            ],
            [
                'id'          => '4',
                'name'        => 'gozine2',
                'displayName' => 'گزینه دو',
                'description' => 'گزینه_دو',
                'order'       => '2',
                'enable'      => '1',
                'created_at'  => '2017-10-12 10:26:05',
                'updated_at'  => '2017-10-12 10:26:05',
                'deleted_at'  => null,
            ],
            [
                'id'          => '5',
                'name'        => 'sanjesh',
                'displayName' => 'سنجش',
                'description' => 'سنجش',
                'order'       => '3',
                'enable'      => '1',
                'created_at'  => '2017-10-12 10:26:05',
                'updated_at'  => '2017-10-12 10:26:05',
                'deleted_at'  => null,
            ],
            [
                'id'          => '6',
                'name'        => 'konkoor',
                'displayName' => 'کنکور سراسری',
                'description' => 'کنکور_سراسری',
                'order'       => '4',
                'enable'      => '1',
                'created_at'  => '2017-10-12 10:26:05',
                'updated_at'  => '2017-10-12 10:26:05',
                'deleted_at'  => null,
            ],
            [
                'id'          => '7',
                'name'        => 'book',
                'displayName' => 'کتاب',
                'description' => 'کتاب',
                'order'       => '3',
                'enable'      => '1',
                'created_at'  => '2017-10-16 11:00:24',
                'updated_at'  => '2017-10-16 11:00:24',
                'deleted_at'  => null,
            ],
            [
                'id'          => '8',
                'name'        => 'video',
                'displayName' => 'فیلم',
                'description' => 'فیلم',
                'order'       => '4',
                'enable'      => '1',
                'created_at'  => '2018-03-04 17:39:17',
                'updated_at'  => '2018-03-04 17:39:17',
                'deleted_at'  => null,
            ],
            [
                'id'          => '9',
                'name'        => 'article',
                'displayName' => 'مقاله',
                'description' => 'مقاله',
                'order'       => '5',
                'enable'      => '1',
                'created_at'  => '2018-03-04 17:39:17',
                'updated_at'  => '2018-03-04 17:39:17',
                'deleted_at'  => null,
            ],
        ];

        DB::table('contenttypes')
          ->insert($data); // Query Builder
    }
}
