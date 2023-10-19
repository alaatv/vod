<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KunkurReginsSeeder extends Seeder
{

    public function run()
    {
        $data = [
            [
                'title' => 'منطقه 1'
            ],
            [
                'title' => 'منطقه 2'
            ],
            [
                'title' => 'منطقه 3'
            ],
            [
                'title' => 'شاهد'
            ],
            [
                'title' => 'هیئت علمی'
            ],
        ];
        DB::table('kunkur_regions')
            ->delete();
        DB::table('kunkur_regions')
            ->insert($data);
    }
}
