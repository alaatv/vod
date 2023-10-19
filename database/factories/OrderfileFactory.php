<?php

namespace Database\Factories;

use App\Models\Orderfile;
use App\Order;
use App\Orderfile;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderfileFactory extends Factory
{
    protected $model = Orderfile::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::pluck('id')->toArray();
        return [
            'order_id' => Order::first() ?? Order::factory(),
            'user_id' => $this->faker->randomElement($users),
            'file' => 'https://nodes.alaatv.com/upload/images/order_receipt_sample.jpg',
            'description' => $this->faker->realText(),
        ];
    }
}
