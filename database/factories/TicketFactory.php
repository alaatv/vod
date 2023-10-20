<?php

namespace Database\Factories;

use App\Models\TicketDepartment;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $users = User::pluck('id')->toArray();
        $departments = TicketDepartment::where('enable', true)->where('display', true)->pluck('id')->toArray();
        $priorities = TicketPriority::pluck('id')->toArray();
        return [
            'user_id' => $this->faker->randomElement($users),
            'department_id' => $this->faker->randomElement($departments),
            'priority_id' => $this->faker->randomElement($priorities),
            'title' => $this->faker->realText(50),
            'status_id' => $this->faker->randomElement([1, 2, 3, 4]),
        ];
    }
}
