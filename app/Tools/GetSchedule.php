<?php

declare(strict_types=1);

namespace App\Tools;

use Illuminate\Support\Facades\Validator;

/** Returns a dog's active schedules, optionally filtered by type. */
class GetSchedule extends BaseTool
{
    public function __invoke(array $input): array
    {
        $validated = Validator::make($input, [
            'dog_id' => ['nullable', 'integer', 'min:1'],
            'type' => ['nullable', 'in:feeding,walk,medication,vet'],
        ])->validate();

        $dog = $this->resolveDog(isset($validated['dog_id']) ? (int) $validated['dog_id'] : null);
        $query = $dog->schedules()->where('is_active', true);

        if (isset($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        $schedules = $query->orderBy('time_of_day')
            ->get(['id', 'type', 'title', 'time_of_day', 'days_of_week'])
            ->map(fn ($schedule): array => [
                'id' => $schedule->id,
                'type' => $schedule->type,
                'title' => $schedule->title,
                'time_of_day' => $schedule->time_of_day,
                'days_of_week' => $schedule->days_of_week,
            ])
            ->toArray();

        return [
            'dog' => $dog->name,
            'schedules' => $schedules,
        ];
    }
}
