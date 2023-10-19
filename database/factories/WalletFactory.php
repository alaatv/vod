<?php

namespace Database\Factories;

use App\User;
use App\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::pluck('id')->toArray();
        $wallettypes = [
            1, //main
            2 //gift
        ];
        return [
            'user_id' => $this->faker->randomElement($users),
            'wallettype_id' => $this->faker->randomElement($wallettypes),
        ];
    }
}
