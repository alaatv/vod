<?php

namespace Database\Factories;

use App\Models\ReferralRequest;
use App\ReferralRequest;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferralCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $referralRequestIds = ReferralRequest::pluck('id')->toArray();
        return [
            'owner_id' => 1,
            'referralRequest_id' => $this->faker->randomElement($referralRequestIds),
            'code' => randomNumber(2) . '-' . randomNumber(5),
            'enable' => 1,
        ];
    }
}
