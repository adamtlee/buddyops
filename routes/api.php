<?php

use App\Http\Controllers\Api\V1\Auth\TokenController;
use App\Http\Controllers\Api\V1\DogActivityController;
use App\Http\Controllers\Api\V1\DogController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('/auth/token', [TokenController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('auth.token.store');

    Route::middleware(['auth:sanctum', 'abilities:api:read'])->group(function (): void {
        Route::delete('/auth/token', [TokenController::class, 'destroy'])->name('auth.token.destroy');
        Route::get('/ping', [TokenController::class, 'ping'])->name('ping');
        Route::get('/me', [TokenController::class, 'me'])->name('me');
        Route::get('/dogs', [DogController::class, 'index'])->name('dogs.index');
        Route::get('/dogs/{dog}', [DogController::class, 'show'])->name('dogs.show');
        Route::get('/dogs/{dog}/{resource}', DogActivityController::class)
            ->whereIn('resource', ['feedings', 'walks', 'play-sessions', 'vet-visits', 'health-flags', 'schedules', 'documents'])
            ->name('dogs.activity.index');
    });
});
