<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DogActivityResource;
use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DogActivityController extends Controller
{
    private const RELATIONSHIPS = [
        'feedings' => 'feedings',
        'walks' => 'walks',
        'play-sessions' => 'playSessions',
        'vet-visits' => 'vetVisits',
        'health-flags' => 'healthFlags',
        'schedules' => 'schedules',
        'documents' => 'documents',
    ];

    public function __invoke(Request $request, Dog $dog, string $resource): AnonymousResourceCollection
    {
        $relationship = self::RELATIONSHIPS[$resource] ?? abort(404);
        $ownedDog = $request->user()->dogs()->whereKey($dog->getKey())->firstOrFail();

        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $records = $ownedDog->{$relationship}()
            ->latest()
            ->paginate($validated['per_page'] ?? 20);

        return DogActivityResource::collection($records);
    }
}
