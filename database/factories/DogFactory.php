<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dog>
 */
class DogFactory extends Factory
{
    protected $model = Dog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->firstName(),
            'breed' => fake()->randomElement(['Labrador Retriever', 'Border Collie', 'Beagle', 'Mixed breed']),
            'birthdate' => fake()->dateTimeBetween('-12 years', '-1 year')->format('Y-m-d'),
            'sex' => fake()->randomElement(['male', 'female']),
            'weight_kg' => fake()->randomFloat(2, 4, 45),
            'allergies' => fake()->optional(0.25)->randomElement(['Chicken', 'Seasonal pollen', 'Beef']),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}