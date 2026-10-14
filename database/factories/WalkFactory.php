<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\Walk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Walk>
 */
class WalkFactory extends Factory
{
    protected $model = Walk::class;

    public function definition(): array
    {
        return [
            'dog_id' => Dog::factory(),
            'started_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'duration_min' => fake()->numberBetween(15, 75),
            'distance_km' => fake()->randomFloat(2, 0.5, 8),
            'notes' => fake()->optional()->sentence(),
            'source' => 'web',
        ];
    }
}