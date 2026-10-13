<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feeding extends Model
{
    protected $fillable = [
        'dog_id', 'fed_at', 'food_type', 'amount_g',
        'portion_eaten', 'notes', 'source',
    ];

    protected function casts(): array
    {
        return [
            'fed_at' => 'datetime',
        ];
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }
}
