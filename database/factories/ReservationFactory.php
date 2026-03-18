<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            "status" => fake()->randomElement(['pending','paid','canceled']),
            "total_price" => fake()->numberBetween(100, 1000),
            "user_id" => User::factory(),
            "session_id" => Session::factory()
        ];
    }
}
