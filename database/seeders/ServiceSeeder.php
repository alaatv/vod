<?php

namespace Database\Seeders;

use App\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Service::truncate();
        DB::table('services')->insert([
            [
                'name' => 'alaa',
                'display_name' => 'آلا',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'soalaa',
                'display_name' => 'سوالا',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'app update',
                'display_name' => 'آپدیت اپلیکیشن',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
