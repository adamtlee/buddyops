<?php

declare(strict_types=1);

namespace App\Tools;

use Illuminate\Support\Facades\Validator;

/** Returns recent vet visits for one of the owning user's dogs. */
class GetVetHistory extends BaseTool
{
    public function __invoke(array $input): array
    {
        $validated = Validator::make($input, [
            'dog_id' => ['nullable', 'integer', 'min:1'],
            'since' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'between:1,20'],
        ])->validate();

        $dog = $this->resolveDog(isset($validated['dog_id']) ? (int) $validated['dog_id'] : null);
        $query = $dog->vetVisits()->orderByDesc('visited_on');

        if (isset($validated['since'])) {
            $query->whereDate('visited_on', '>=', $validated['since']);
        }

        $visits = $query->limit($validated['limit'] ?? 5)
            ->get(['visited_on', 'clinic', 'reason', 'diagnosis', 'notes', 'next_due_on'])
            ->map(fn ($visit): array => [
                'visited_on' => $visit->visited_on?->toDateString(),
                'clinic' => $visit->clinic,
                'reason' => $visit->reason,
                'diagnosis' => $visit->diagnosis,
                'notes' => $visit->notes,
                'next_due_on' => $visit->next_due_on?->toDateString(),
            ])
            ->toArray();

        return [
            'dog' => $dog->name,
            'visits' => $visits,
        ];
    }
}
