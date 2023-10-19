<?php

namespace Database\Seeders\Coupons;

use App\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Coupon::factory()->count(20)->create();
    }
}
