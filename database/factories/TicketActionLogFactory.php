<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TicketActionLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ticket_message_id' => 1,
            'user_id' => 1,
            'action_id' => 1,
            'before' => null,
            'after' => null
        ];
    }
}
