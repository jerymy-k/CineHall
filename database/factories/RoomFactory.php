<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            "name" => fake()->sentence(3),
            "total_seats" => fake()->numberBetween(10, 60),
            "is_vip" => fake()->boolean(),
        ];
    }
}
