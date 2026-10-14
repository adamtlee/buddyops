<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'dog_id',
        'role',
        'content',
        'tool_calls',
        'tool_call_id',
        'is_error',
        'created_at',
        'updated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tool_calls' => 'array',
            'is_error' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(Dog::class);
    }
}
