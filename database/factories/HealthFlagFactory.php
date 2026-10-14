<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\HealthFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthFlag>
 */
class HealthFlagFactory extends Factory
{
    protected $model = HealthFlag::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['open', 'sent_to_vet', 'resolved']);

        return [
            'dog_id' => Dog::factory(),
            'flagged_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'description' => fake()->randomElement(['Reduced appetite', 'Limping after a walk', 'Mild skin irritation']),
            'severity' => fake()->randomElement(['low', 'medium', 'high']),
            'status' => $status,
            'created_by' => fake()->randomElement(['user', 'agent']),
            'resolved_at' => $status === 'resolved' ? now() : null,
        ];
    }
}