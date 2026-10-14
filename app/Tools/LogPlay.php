<?php

declare(strict_types=1);

namespace App\Tools;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

/** Logs a play session for one of the owning user's dogs. */
class LogPlay extends BaseTool
{
    public function __invoke(array $input): array
    {
        $validated = Validator::make($input, [
            'dog_id' => ['nullable', 'integer', 'min:1'],
            'played_at' => ['nullable', 'date'],
            'activity' => ['nullable', 'in:fetch,tug,training,other'],
            'duration_min' => ['required', 'integer', 'between:1,600'],
            'energy_level' => ['nullable', 'integer', 'between:1,5'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'source' => ['nullable', 'in:web,chat'],
        ])->validate();

        $dog = $this->resolveDog(isset($validated['dog_id']) ? (int) $validated['dog_id'] : null);
        $playedAt = Carbon::parse($validated['played_at'] ?? now());
        $activity = $validated['activity'] ?? 'other';

        $session = $dog->playSessions()->create([
            'played_at' => $playedAt,
            'activity' => $activity,
            'duration_min' => $validated['duration_min'],
            'energy_level' => $validated['energy_level'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'source' => $validated['source'] ?? 'web',
        ]);

        return [
            'ok' => true,
            'id' => $session->id,
            'dog' => $dog->name,
            'played_at' => $session->played_at->toIso8601String(),
            'message' => "Logged a {$session->duration_min}-minute {$activity} session for {$dog->name}.",
        ];
    }
}
