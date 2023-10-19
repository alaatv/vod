<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockTypesSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $table = 'block_types';

        DB::table($table)->delete();
        DB::table($table)->insert([
            array('id' => '1','name' => 'home','display_name' => 'صفحه اصلی','description' => NULL,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '2','name' => 'shop','display_name' => 'فروشگاه','description' => NULL,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '3','name' => 'product','display_name' => 'صفحه محصولات','description' => NULL,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '4','name' => '4th block type','display_name' => 'بلاک نوع چهارم','description' => NULL,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '5','name' => '5th block type','display_name' => 'بلاک نوع پنجم','description' => NULL,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '6','name' => '6th block type','display_name' => 'بلاک نوع ششم','description' => NULL,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
            array('id' => '7','name' => 'banners','display_name' => 'بلوک بنرهای تبلیغاتی','description' => NULL,'created_at' => Carbon::now(),'updated_at' => Carbon::now()),
        ]);
    }
}
