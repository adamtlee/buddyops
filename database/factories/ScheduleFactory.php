<?php

namespace Database\Factories;

use App\Models\Dog;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['feeding', 'walk', 'medication', 'vet']);

        return [
            'dog_id' => Dog::factory(),
            'type' => $type,
            'title' => ucfirst($type).' reminder',
            'time_of_day' => fake()->time('H:i:s'),
            'days_of_week' => fake()->randomElements(range(0, 6), fake()->numberBetween(3, 7)),
            'is_active' => fake()->boolean(90),
        ];
    }
}