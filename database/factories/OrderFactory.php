<?php

namespace Database\Factories;

use App\Coupon;
use App\Order;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Order::class;

    public function definition()
    {
        $users = User::pluck('id')->toArray();
        $coupons = Coupon::pluck('id')->toArray();
        return [
            'user_id' => $this->faker->randomElement($users),
            'orderstatus_id' => $this->faker->randomElement([2, 3]),
            'paymentstatus_id' => $this->faker->randomElement([1, 2, 3, 4]),
            'coupon_id' => $this->faker->randomElement($coupons),
            'couponDiscount' => 10,
            'cost' => 90000,
            'costwithoutcoupon' => 100000,
            'isInInstalment' => $this->faker->randomElement([0, 1]),
            'customerDescription' => $this->faker->realText(),
            'completed_at' => now()->addMinutes(random_int(4, 10)),
        ];
    }
}
