<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VetVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'dog_id', 'visited_on', 'clinic', 'reason',
        'diagnosis', 'notes', 'next_due_on',
    ];

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
            'next_due_on' => 'date',
        ];
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }
}
