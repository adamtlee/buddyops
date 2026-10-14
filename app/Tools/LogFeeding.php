<?php

declare(strict_types=1);

namespace App\Tools;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

/** Logs a feeding record for one of the owning user's dogs. */
class LogFeeding extends BaseTool
{
    public function __invoke(array $input): array
    {
        $validated = Validator::make($input, [
            'dog_id' => ['nullable', 'integer', 'min:1'],
            'fed_at' => ['nullable', 'date'],
            'food_type' => ['required', 'string', 'max:255'],
            'amount_g' => ['nullable', 'integer', 'between:1,5000'],
            'portion_eaten' => ['nullable', 'in:all,most,some,none'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'source' => ['nullable', 'in:web,chat'],
        ])->validate();

        $dog = $this->resolveDog(isset($validated['dog_id']) ? (int) $validated['dog_id'] : null);
        $fedAt = Carbon::parse($validated['fed_at'] ?? now());
        $portionEaten = $validated['portion_eaten'] ?? 'all';

        $feeding = $dog->feedings()->create([
            'fed_at' => $fedAt,
            'food_type' => $validated['food_type'],
            'amount_g' => $validated['amount_g'] ?? null,
            'portion_eaten' => $portionEaten,
            'notes' => $validated['notes'] ?? null,
            'source' => $validated['source'] ?? 'web',
        ]);

        return [
            'ok' => true,
            'id' => $feeding->id,
            'dog' => $dog->name,
            'fed_at' => $feeding->fed_at->toIso8601String(),
            'message' => "Logged feeding for {$dog->name}: {$validated['food_type']}, ate {$portionEaten}.",
        ];
    }
}
