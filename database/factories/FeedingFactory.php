<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\Feeding;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feeding>
 */
class FeedingFactory extends Factory
{
    protected $model = Feeding::class;

    public function definition(): array
    {
        return [
            'dog_id' => Dog::factory(),
            'fed_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'food_type' => fake()->randomElement(['Adult kibble', 'Wet food', 'Chicken and rice']),
            'amount_g' => fake()->numberBetween(100, 600),
            'portion_eaten' => fake()->randomElement(['all', 'most', 'some', 'none']),
            'notes' => fake()->optional()->sentence(),
            'source' => 'web',
        ];
    }
}