<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            "description" => fake()->text(1000),
            "image" => fake()->imageUrl(600, 400, "movies", true),
            "duration" => fake()->numberBetween(60, 200),
            "min_age" => fake()->randomElement([7, 10, 13, 16, 18]),
            "trailer" => fake()->url()
        ];
    }
}
