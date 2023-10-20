<?php

namespace Database\Factories;

use App\Models\BlockType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockFactory extends Factory
{
    public function definition()
    {
        return [
            'type' => BlockType::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence,
            'order' => random_int(1, 10), // 1 and 10 not important i just want an integer :|
            'enable' => true,
        ];
    }
}
