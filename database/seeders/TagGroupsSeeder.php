<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagGroupsSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now('Asia/Tehran');

        $table = 'tag_groups';

        DB::table($table)->delete();
        DB::table($table)->insert([
            array('id' => '1','name' => 'educational_system','display_name' => 'نظام آموزشی','enable' => '1','description' => 'مشخص کننده نظام آموزشی قدیم یا جدید','created_at' => $now,'updated_at' => $now,'deleted_at' => NULL),
            array('id' => '2','name' => 'grade','display_name' => 'مقطع تحصیلی','enable' => '1','description' => NULL,'created_at' => $now,'updated_at' => $now,'deleted_at' => NULL),
            array('id' => '3','name' => 'major','display_name' => 'رشته تحصیلی','enable' => '1','description' => NULL,'created_at' => $now,'updated_at' => $now,'deleted_at' => NULL),
            array('id' => '4','name' => 'lesson','display_name' => 'درس','enable' => '1','description' => NULL,'created_at' => $now,'updated_at' => $now,'deleted_at' => NULL),
            array('id' => '5','name' => 'teacher','display_name' => 'دبیر','enable' => '1','description' => NULL,'created_at' => $now,'updated_at' => $now,'deleted_at' => NULL),
            array('id' => '6','name' => 'tree','display_name' => 'درخت','enable' => '1','description' => NULL,'created_at' => $now,'updated_at' => $now,'deleted_at' => NULL),
        ]);
    }
}
