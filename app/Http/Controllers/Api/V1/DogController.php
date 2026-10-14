<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DogResource;
use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $dogs = $request->user()->dogs()
            ->orderBy('name')
            ->paginate($validated['per_page'] ?? 20);

        return DogResource::collection($dogs);
    }

    public function show(Request $request, Dog $dog): DogResource
    {
        $ownedDog = $request->user()->dogs()->whereKey($dog->getKey())->firstOrFail();

        return new DogResource($ownedDog);
    }
}
