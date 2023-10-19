<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = User::class;

    public function definition()
    {
        return [
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'mobile' => '09'.random_int(100000000, 999999999),
            'nationalCode' => '0000000000',
            'password' => Hash::make('0000000000'),
            'email' => $this->faker->email,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'userstatus_id' => 1,
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'id' => '1',
                'mobile' => '09999999999',
            ];
        });
    }
}
