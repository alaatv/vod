<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'owner_id' => 1,
            'discounttype_id' => 2,
            'discount' => 100000,
            'numberOfCodes' => 10,
            'usageLimit' => 1,
            'default_commission' => random_int(5,20),
        ];
    }
}
