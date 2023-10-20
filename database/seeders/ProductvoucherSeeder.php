<?php

namespace Database\Seeders;

use App\Models\Productvoucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductvoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Productvoucher::truncate();
        Productvoucher::factory()->count(50)->create();
        Schema::enableForeignKeyConstraints();
    }
}
