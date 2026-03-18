<?php

namespace Database\Factories;

use App\Models\ReservedSeat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReservedSeat>
 */
class ReservedSeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            "seat_number" => fake()->numberBetween(20, 100)
        ];
    }
}
