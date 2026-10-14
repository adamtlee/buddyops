<?php

declare(strict_types=1);

namespace App\Tools;

use App\Models\Dog;
use App\Models\User;

abstract class BaseTool
{
    public function __construct(protected User $user) {}

    protected function resolveDog(?int $dogId = null): Dog
    {
        $dogs = $this->user->dogs();

        if ($dogId !== null) {
            return $dogs->findOrFail($dogId);
        }

        return $dogs->orderBy('id')->firstOrFail();
    }
}
