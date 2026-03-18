<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\Movie;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Session>
 */
class SessionFactory extends Factory
{
    public function definition(): array
    {
        $start_date = fake()->dateTime();

        return [
            "start_at" => $start_date,
            "end_at" => fake()->dateTimeBetween($start_date, "+1 year"),
            "language" => fake()->randomElement(['en','zh','hi','es','fr','ar','bn','pt','ru','ur','de']),
            "price" => fake()->numberBetween(10, 1000),
            "room_id" => Room::factory(),
            "movie_id" => Movie::factory()
        ];
    }
}
