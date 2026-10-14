<?php

namespace Database\Seeders;

use App\Models\Dog;
use App\Models\User;
use Illuminate\Database\Seeder;
use LogicException;

class DemoDataSeeder extends Seeder
{
    private const SEEDED_DOG_NOTES = '[buddyops-demo-seed]';

    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new LogicException('Demo data may only be seeded in the local environment.');
        }

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'],
        );

        // Delete only the dogs explicitly marked as demo fixtures; cascading foreign keys
        // remove their activity records while leaving any other user data untouched.
        $user->dogs()->where('notes', self::SEEDED_DOG_NOTES)->delete();

        $dogs = [
            [
                'name' => 'Milo (Demo)',
                'breed' => 'Labrador Retriever',
                'birthdate' => now()->subYears(4)->toDateString(),
                'sex' => 'male',
                'weight_kg' => 29.40,
                'allergies' => null,
            ],
            [
                'name' => 'Poppy (Demo)',
                'breed' => 'Border Collie',
                'birthdate' => now()->subYears(2)->subMonths(3)->toDateString(),
                'sex' => 'female',
                'weight_kg' => 18.20,
                'allergies' => 'Chicken',
            ],
        ];

        foreach ($dogs as $attributes) {
            $dog = Dog::factory()->for($user)->create([
                ...$attributes,
                'notes' => self::SEEDED_DOG_NOTES,
            ]);

            $this->seedHistory($dog);
        }
    }

    private function seedHistory(Dog $dog): void
    {
        for ($daysAgo = 0; $daysAgo < 30; $daysAgo++) {
            $date = now()->subDays($daysAgo);

            foreach ([7, 18] as $hour) {
                $dog->feedings()->create([
                    'fed_at' => $date->copy()->setTime($hour, 0),
                    'food_type' => $dog->name === 'Poppy (Demo)' ? 'Sensitive stomach kibble' : 'Adult salmon kibble',
                    'amount_g' => $dog->name === 'Poppy (Demo)' ? 220 : 340,
                    'portion_eaten' => 'all',
                    'source' => 'seed',
                ]);
            }

            $dog->walks()->create([
                'started_at' => $date->copy()->setTime(8, 0),
                'duration_min' => $daysAgo % 2 === 0 ? 35 : 50,
                'distance_km' => $daysAgo % 2 === 0 ? 2.40 : 3.60,
                'notes' => $daysAgo % 7 === 0 ? 'Park loop' : null,
                'source' => 'seed',
            ]);

            if ($daysAgo % 3 === 0) {
                $dog->playSessions()->create([
                    'played_at' => $date->copy()->setTime(17, 0),
                    'activity' => $daysAgo % 2 === 0 ? 'fetch' : 'training',
                    'duration_min' => 20,
                    'energy_level' => $daysAgo % 2 === 0 ? 4 : 3,
                    'notes' => null,
                    'source' => 'seed',
                ]);
            }
        }

        $dog->vetVisits()->create([
            'visited_on' => now()->subMonths(3)->toDateString(),
            'clinic' => 'Oak Street Veterinary Clinic',
            'reason' => 'Annual wellness exam',
            'diagnosis' => 'Healthy; vaccinations up to date.',
            'next_due_on' => now()->addMonths(9)->toDateString(),
        ]);

        $dog->healthFlags()->createMany([
            [
                'flagged_at' => now()->subDays(12),
                'description' => 'Mild itchiness after playing in the grass.',
                'severity' => 'low',
                'status' => 'resolved',
                'created_by' => 'user',
                'resolved_at' => now()->subDays(10),
            ],
            [
                'flagged_at' => now()->subDays(2),
                'description' => 'Monitoring appetite after a change in treats.',
                'severity' => 'medium',
                'status' => 'open',
                'created_by' => 'user',
                'resolved_at' => null,
            ],
        ]);

        $dog->schedules()->createMany([
            [
                'type' => 'feeding',
                'title' => 'Morning feeding',
                'time_of_day' => '07:00:00',
                'days_of_week' => range(0, 6),
                'is_active' => true,
            ],
            [
                'type' => 'walk',
                'title' => 'Morning walk',
                'time_of_day' => '08:00:00',
                'days_of_week' => range(0, 6),
                'is_active' => true,
            ],
            [
                'type' => 'medication',
                'title' => 'Joint supplement',
                'time_of_day' => '18:00:00',
                'days_of_week' => [1, 3, 5],
                'is_active' => $dog->name === 'Milo (Demo)',
            ],
        ]);

        // These records are metadata-only examples; their paths intentionally do not
        // point to real uploaded files and no document chunks or embeddings are created.
        $dog->documents()->createMany([
            [
                'title' => 'Vaccination record (sample)',
                'type' => 'vet_record',
                'file_path' => 'demo-placeholders/not-a-real-vaccination-record.pdf',
                'status' => 'uploaded',
            ],
            [
                'title' => 'Food label (sample)',
                'type' => 'food_label',
                'file_path' => 'demo-placeholders/not-a-real-food-label.pdf',
                'status' => 'uploaded',
            ],
        ]);
    }
}