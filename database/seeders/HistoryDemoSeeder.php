<?php

namespace Database\Seeders;

use App\Models\Dog;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HistoryDemoSeeder extends Seeder
{
    private const DAYS = 120;

    private const SCHEDULE_TITLES = ['Breakfast', 'Dinner', 'Morning walk', 'Heartworm pill'];

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->warn('WARNING: running in production. This writes seeded history for a dog.');

            if (! $this->command->confirm('Continue?', false)) {
                return;
            }
        }

        mt_srand(42);

        $user = User::query()->orderBy('id')->first()
            ?? User::create(['name' => 'History User', 'email' => 'history@example.com', 'password' => 'password']);

        $dog = $user->dogs()->where('name', 'Buddy')->orderBy('id')->first()
            ?? $user->dogs()->create([
                'name' => 'Buddy',
                'breed' => 'Labrador',
                'weight_kg' => 28.5,
                'allergies' => 'chicken',
            ]);

        foreach (['feedings', 'walks', 'play_sessions', 'vet_visits'] as $table) {
            DB::table($table)->where('dog_id', $dog->id)->where('source', 'seed')->delete();
        }
        Schedule::where('dog_id', $dog->id)->whereIn('title', self::SCHEDULE_TITLES)->delete();

        $now = now();
        $today = Carbon::today();
        $at = fn (int $offset): Carbon => $today->copy()->subDays($offset);

        // Offsets are days before today.
        $appetiteDays = range(21, 15);
        $playGapDays = range(21, 12);
        $vetOffset = 14;
        $lowEnergyDays = range(13, 4);
        $checkupOffset = 100;

        $feedings = $walks = $plays = [];
        $base = ['dog_id' => $dog->id, 'source' => 'seed', 'created_at' => $now, 'updated_at' => $now];

        for ($offset = self::DAYS - 1; $offset >= 0; $offset--) {
            $day = $at($offset);

            foreach ([[7, 30], [18, 0]] as [$h, $m]) {
                $time = $day->copy()->setTime($h, $m)->addMinutes(mt_rand(-30, 30));
                $portion = mt_rand(1, 100) <= 90 ? 'all' : 'most';

                if (in_array($offset, $appetiteDays, true) && mt_rand(1, 100) <= 60) {
                    $portion = mt_rand(0, 1) ? 'some' : 'none';
                }

                if ($time->isFuture()) {
                    continue;
                }

                $feedings[] = $base + [
                    'fed_at' => $time, 'food_type' => 'kibble', 'amount_g' => mt_rand(180, 220),
                    'portion_eaten' => $portion, 'notes' => null,
                ];
            }

            if (mt_rand(1, 12) !== 1) {
                $starts = [$day->copy()->setTime(8, 0)->addMinutes(mt_rand(-20, 20))];
                if (mt_rand(0, 1)) {
                    $starts[] = $day->copy()->setTime(17, 30)->addMinutes(mt_rand(-20, 20));
                }

                foreach ($starts as $start) {
                    $duration = in_array($offset, $lowEnergyDays, true) ? mt_rand(10, 20) : mt_rand(20, 60);
                    $distance = mt_rand(100, 450) / 100;

                    if ($start->isFuture()) {
                        continue;
                    }

                    $walks[] = $base + [
                        'started_at' => $start, 'duration_min' => $duration, 'distance_km' => $distance, 'notes' => null,
                    ];
                }
            }

            $playRoll = mt_rand(1, 7);
            if ($playRoll <= 4 && ! in_array($offset, $playGapDays, true)) {
                $activityRoll = mt_rand(1, 100);
                $activity = match (true) {
                    $activityRoll <= 50 => 'fetch',
                    $activityRoll <= 70 => 'tug',
                    $activityRoll <= 90 => 'training',
                    default => 'other',
                };
                $time = $day->copy()->setTime(mt_rand(15, 19), mt_rand(0, 59));

                if (! $time->isFuture()) {
                    $plays[] = $base + [
                        'played_at' => $time, 'activity' => $activity, 'duration_min' => mt_rand(10, 30),
                        'energy_level' => mt_rand(3, 5), 'notes' => null,
                    ];
                }
            }
        }

        $vetDate = $at($vetOffset);
        $checkupDate = $at($checkupOffset);
        $vets = [
            $base + [
                'visited_on' => $checkupDate->toDateString(), 'clinic' => null, 'reason' => 'Annual checkup',
                'diagnosis' => null, 'notes' => null, 'next_due_on' => $checkupDate->copy()->addYear()->toDateString(),
            ],
            $base + [
                'visited_on' => $vetDate->toDateString(), 'clinic' => null, 'reason' => 'Not eating well',
                'diagnosis' => 'Mild stomach upset', 'notes' => 'Bland diet for 3 days; recheck if it continues',
                'next_due_on' => $vetDate->copy()->addMonthsNoOverflow(6)->toDateString(),
            ],
        ];

        foreach ([['feedings', $feedings], ['walks', $walks], ['play_sessions', $plays], ['vet_visits', $vets]] as [$table, $rows]) {
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table($table)->insert($chunk);
            }
        }

        $every = [0, 1, 2, 3, 4, 5, 6];
        foreach ([
            ['feeding', 'Breakfast', '07:30:00', $every],
            ['feeding', 'Dinner', '18:00:00', $every],
            ['walk', 'Morning walk', '08:00:00', $every],
            ['medication', 'Heartworm pill', '09:00:00', [1]],
        ] as [$type, $title, $time, $days]) {
            Schedule::create([
                'dog_id' => $dog->id, 'type' => $type, 'title' => $title,
                'time_of_day' => $time, 'days_of_week' => $days, 'is_active' => true,
            ]);
        }

        $notAll = count(array_filter($feedings, fn ($f) => $f['portion_eaten'] !== 'all'));
        $this->writeTruth($dog, $today, $at, $appetiteDays, $playGapDays, $vetOffset, $lowEnergyDays, $checkupOffset, [
            'feedings' => count($feedings), 'walks' => count($walks), 'play' => count($plays),
            'vets' => count($vets), 'notAll' => $notAll,
        ]);

        $this->command->info("Seeded history for {$dog->name} (dog #{$dog->id}, user #{$user->id}).");
    }

    private function writeTruth(Dog $dog, Carbon $today, callable $at, array $appetite, array $playGap, int $vet, array $lowEnergy, int $checkup, array $c): void
    {
        $range = fn (array $offsets): string => $at(max($offsets))->toDateString().' to '.$at(min($offsets))->toDateString();
        $first = $at(self::DAYS - 1)->toDateString();

        $md = <<<MD
# Seed ground truth

Generated by `HistoryDemoSeeder` for dog "{$dog->name}" (id {$dog->id}). Seeded history spans {$first} to {$today->toDateString()} (120 days). All seeded rows have `source = 'seed'`.

## Planted patterns

1. **Reduced appetite**: {$range($appetite)} (7 consecutive days). About 60% of feedings in this window have `portion_eaten` of `some` or `none`.
2. **Play drop**: {$range($playGap)} (the 7 appetite days plus the 3 following days). No play sessions at all.
3. **Vet visit**: {$at($vet)->toDateString()}. Reason "Not eating well", diagnosis "Mild stomach upset", notes "Bland diet for 3 days; recheck if it continues", next due {$at($vet)->copy()->addMonthsNoOverflow(6)->toDateString()}.
4. **Annual checkup**: {$at($checkup)->toDateString()}. Reason "Annual checkup".
5. **Low energy**: {$range($lowEnergy)} (10 days after the vet visit). Walk `duration_min` is 10 to 20.

## Counts (seeded rows only)

| Item | Count |
| --- | --- |
| Feedings | {$c['feedings']} |
| Walks | {$c['walks']} |
| Play sessions | {$c['play']} |
| Vet visits | {$c['vets']} |
| Feedings where portion_eaten is not 'all' | {$c['notAll']} |

Entries timestamped in the future (later today) are not generated.

MD;

        file_put_contents(base_path('docs/seed-truth.md'), $md);
    }
}
