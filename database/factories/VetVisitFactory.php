<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\VetVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VetVisit>
 */
class VetVisitFactory extends Factory
{
    protected $model = VetVisit::class;

    public function definition(): array
    {
        return [
            'dog_id' => Dog::factory(),
            'visited_on' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'clinic' => fake()->optional()->company().' Veterinary Clinic',
            'reason' => fake()->randomElement(['Annual wellness exam', 'Vaccination', 'Skin irritation']),
            'diagnosis' => fake()->optional()->sentence(),
            'notes' => fake()->optional()->sentence(),
            'next_due_on' => fake()->optional()->dateTimeBetween('now', '+1 year')?->format('Y-m-d'),
        ];
    }
}