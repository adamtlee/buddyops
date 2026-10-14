<?php

declare(strict_types=1);

namespace App\Tools;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

/** Logs a walk record for one of the owning user's dogs. */
class LogWalk extends BaseTool
{
    public function __invoke(array $input): array
    {
        $validated = Validator::make($input, [
            'dog_id' => ['nullable', 'integer', 'min:1'],
            'started_at' => ['nullable', 'date'],
            'duration_min' => ['required', 'integer', 'between:1,600'],
            'distance_km' => ['nullable', 'numeric', 'between:0,100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'source' => ['nullable', 'in:web,chat'],
        ])->validate();

        $dog = $this->resolveDog(isset($validated['dog_id']) ? (int) $validated['dog_id'] : null);
        $startedAt = Carbon::parse($validated['started_at'] ?? now());

        $walk = $dog->walks()->create([
            'started_at' => $startedAt,
            'duration_min' => $validated['duration_min'],
            'distance_km' => $validated['distance_km'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'source' => $validated['source'] ?? 'web',
        ]);

        return [
            'ok' => true,
            'id' => $walk->id,
            'dog' => $dog->name,
            'started_at' => $walk->started_at->toIso8601String(),
            'message' => "Logged a {$walk->duration_min}-minute walk for {$dog->name}.",
        ];
    }
}
