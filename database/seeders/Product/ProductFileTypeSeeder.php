<?php

namespace Database\Seeders\Product;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductFileTypeSeeder extends Seeder
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
                'name' => 'pamphlet',
                'displayName' => 'جزوه',
                'description' => 'فایل از نوع جزوه است',
            ],
            [
                'id' => '2',
                'name' => 'video',
                'displayName' => 'فیلم',
                'description' => 'فایل از نوع فیلم است',
            ],
        ];

        DB::table('productfiletypes')
            ->insert($data); // Query Builder
    }
}
