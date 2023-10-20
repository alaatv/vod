<?php

namespace Database\Seeders\Other;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PartialCouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()
            ->has(
                Coupon::factory()
                ->state([
                    'coupontype_id' => 2,

                ])
            )
            ->count(5)
            ->create();
    }
}
