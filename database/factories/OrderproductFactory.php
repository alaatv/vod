<?php

namespace Database\Factories;

use App\Order;
use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderproductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $orders = Order::pluck('id')->toArray();
        $products = Product::pluck('id')->toArray();
        return [
            'orderproducttype_id' => 1,
            'order_id' => $this->faker->randomElement($orders),
            'product_id' => $this->faker->randomElement($products),
            'cost' => 10000,
            'tmp_final_cost' => 10000,
            'includedInCoupon' => 0,
        ];
    }
}
