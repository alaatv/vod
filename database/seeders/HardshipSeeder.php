<?php

namespace Database\Seeders;

use App\Models\Hardship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HardshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Hardship::truncate();
        DB::table('hardships')->insert([
            [
                'name' => 'preliminary',
                'display_name' => 'مقدماتی',
                'specifier' => 'dana',
                'specifier_value' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'intermediate',
                'display_name' => 'متوسط',
                'specifier' => 'dana',
                'specifier_value' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'advanced',
                'display_name' => 'پیشرفته',
                'specifier' => 'dana',
                'specifier_value' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'preliminary to advanced',
                'display_name' => 'مقدماتی تا پیشرفته',
                'specifier' => 'dana',
                'specifier_value' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'from zero to infinity',
                'display_name' => 'از صفر تا بی نهایت',
                'specifier' => 'dana',
                'specifier_value' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '3 years in 90 days',
                'display_name' => '۳ سال در ۹۰ روز',
                'specifier' => 'dana',
                'specifier_value' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        Schema::enableForeignKeyConstraints();
    }
}
