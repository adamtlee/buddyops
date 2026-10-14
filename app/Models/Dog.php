<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'breed', 'birthdate',
        'sex', 'weight_kg', 'allergies', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'weight_kg' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedings(): HasMany
    {
        return $this->hasMany(Feeding::class);
    }

    public function walks(): HasMany
    {
        return $this->hasMany(Walk::class);
    }

    public function playSessions(): HasMany
    {
        return $this->hasMany(PlaySession::class);
    }

    public function vetVisits(): HasMany
    {
        return $this->hasMany(VetVisit::class);
    }

    public function healthFlags(): HasMany
    {
        return $this->hasMany(HealthFlag::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}