<?php

namespace Database\Seeders\Product;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()
            ->state([
                'category' => 'vip',
                'isFree' => 0,
                'enable' => 1,
            ])
            ->count(10)
            ->create();
    }
}
