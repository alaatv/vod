<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankaccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userIds = User::pluck('id')->toArray();
        $bankIds = Bank::pluck('id')->toArray();
        return [
            'user_id' => $this->faker->randomElement($userIds),
            'bank_id' => $this->faker->randomElement($bankIds),
            'accountNumber' => randomNumber(18),
            'cardNumber' => randomNumber(16),
            'preShabaNumber' => 'IR',
            'shabaNumber' => randomNumber(24),
        ];
    }
}
