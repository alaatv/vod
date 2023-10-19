<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        DB::table('producttypes')
          ->delete();
        $data =[
            [
                'id'          => '1',
                'displayName' => 'ساده',
                'name'        => 'simple',
                'description' => 'کالای بدون انواع صفت مانند رنگهای مختلف',
                'created_at'  => null,
                'updated_at'  => '2018-10-13 15:30:48',
                'deleted_at'  => null,
            ],
            [
                'id'          => '2',
                'displayName' => 'قابل پیکربندی',
                'name'        => 'configurable',
                'description' => 'کالای دارای انواع مختلف صفت مانند رنگ های مختلف',
                'created_at'  => null,
                'updated_at'  => '2018-10-13 15:30:48',
                'deleted_at'  => null,
            ],
            [
                'id'          => '3',
                'displayName' => 'قابل انتخاب',
                'name'        => 'selectable',
                'description' => 'کالا قابل انتخاب از بین کالاهای زیر مجموعه خود است',
                'created_at'  => '2017-08-13 16:40:08',
                'updated_at'  => '2018-10-15 18:10:59',
                'deleted_at'  => null,
            ],
        ];
        DB::table('producttypes')
          ->insert($data); // Query Builder
    }
}
