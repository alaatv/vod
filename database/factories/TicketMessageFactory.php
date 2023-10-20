<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TicketMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $files = ['photos' => null, 'voices' => null, 'file' => null];
        return [
            'user_id' => 1,
            'body' => $this->faker->realText,
            'files' => $files
        ];
    }
}
