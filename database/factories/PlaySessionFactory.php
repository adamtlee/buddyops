<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\PlaySession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlaySession>
 */
class PlaySessionFactory extends Factory
{
    protected $model = PlaySession::class;

    public function definition(): array
    {
        return [
            'dog_id' => Dog::factory(),
            'played_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'activity' => fake()->randomElement(['fetch', 'tug', 'training', 'other']),
            'duration_min' => fake()->numberBetween(5, 60),
            'energy_level' => fake()->numberBetween(1, 5),
            'notes' => fake()->optional()->sentence(),
            'source' => 'web',
        ];
    }
}