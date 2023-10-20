<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Coupon::class;

    public function definition()
    {
        $requiredProducts = Product::all()->random(5)->pluck('id')->toArray();
        $key = array_rand([$requiredProducts, null]);
        $requiredProducts = $key ? json_encode($requiredProducts) : null;
        $unRequiredProducts = $key ? json_encode(Product::all()->except($requiredProducts)->random(5)->pluck('id')->toArray()) : null;
        return [
            'coupontype_id' => $this->faker->randomElement([1, 2]),
            'discounttype_id' => 1,
            'required_products' => $requiredProducts,
            'unrequired_products' => $unRequiredProducts,
            'name' => Str::random(5),
            'code' => Str::random(5),
            'discount' => 10,
            'usageLimit' => $this->faker->randomElement([1, null]),
        ];
    }
}

