<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthFlag extends Model
{
    protected $fillable = [
        'dog_id', 'flagged_at', 'description', 'severity',
        'status', 'created_by', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'flagged_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }
}
