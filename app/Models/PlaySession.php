<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'dog_id', 'played_at', 'activity', 'duration_min',
        'energy_level', 'notes', 'source',
    ];

    protected function casts(): array
    {
        return [
            'played_at' => 'datetime',
        ];
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }
}
