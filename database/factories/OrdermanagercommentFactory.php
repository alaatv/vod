<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Ordermanagercomment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdermanagercommentFactory extends Factory
{
    protected $model = Ordermanagercomment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::pluck('id')->toArray();
        return [
            'user_id' => $this->faker->randomElement($users),
            'order_id' => Order::first() ?? Order::factory(),
            'comment' => $this->faker->realText()
        ];
    }
}
