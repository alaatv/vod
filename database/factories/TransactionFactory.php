<?php

namespace Database\Factories;

use App\Order;
use App\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Transaction::class;

    public function definition()
    {
        $orders = Order::pluck('id')->toArray();
        return [
            'order_id' => $this->faker->randomElement($orders),
            'cost' => 100000,
            'transactionID' => Str::random(20),
            'paymentmethod_id' => 1,
            'device_id' => 1,
            'transactiongateway_id' => 5,
            'transactionstatus_id' => 3,
            'completed_at' => now()->addMinutes(5),
        ];
    }
}
