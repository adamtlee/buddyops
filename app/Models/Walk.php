<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Walk extends Model
{
    protected $fillable = [
        'dog_id', 'started_at', 'duration_min', 'distance_km',
        'notes', 'source',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'distance_km' => 'decimal:2',
        ];
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }
}
