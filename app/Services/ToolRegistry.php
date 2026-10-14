<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Tools\GetSchedule;
use App\Tools\GetVetHistory;
use App\Tools\LogFeeding;
use App\Tools\LogPlay;
use App\Tools\LogWalk;
use InvalidArgumentException;

final class ToolRegistry
{
    /** @var array<string, class-string> */
    private const TOOL_CLASSES = [
        'log_feeding' => LogFeeding::class,
        'log_walk' => LogWalk::class,
        'log_play' => LogPlay::class,
        'get_schedule' => GetSchedule::class,
        'get_vet_history' => GetVetHistory::class,
    ];

    /**
     * @return array<int, array{name: string, description: string, input_schema: array<string, mixed>}>
     */
    public function schemas(): array
    {
        return [
            [
                'name' => 'log_feeding',
                'description' => 'Use when the user says their dog ate, was fed, or refused food and wants to record that feeding.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'fed_at' => ['type' => 'string', 'format' => 'date-time'],
                        'food_type' => ['type' => 'string', 'maxLength' => 255],
                        'amount_g' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 5000],
                        'portion_eaten' => ['type' => 'string', 'enum' => ['all', 'most', 'some', 'none']],
                        'notes' => ['type' => 'string', 'maxLength' => 1000],
                    ],
                    'required' => ['food_type'],
                    'additionalProperties' => false,
                ],
            ],
            [
                'name' => 'log_walk',
                'description' => 'Use when the user wants to record a dog walk, including its duration or distance.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'started_at' => ['type' => 'string', 'format' => 'date-time'],
                        'duration_min' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 600],
                        'distance_km' => ['type' => 'number', 'minimum' => 0, 'maximum' => 100],
                        'notes' => ['type' => 'string', 'maxLength' => 1000],
                    ],
                    'required' => ['duration_min'],
                    'additionalProperties' => false,
                ],
            ],
            [
                'name' => 'log_play',
                'description' => 'Use when the user wants to record a play session such as fetch, tug, or training.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'played_at' => ['type' => 'string', 'format' => 'date-time'],
                        'activity' => ['type' => 'string', 'enum' => ['fetch', 'tug', 'training', 'other']],
                        'duration_min' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 600],
                        'energy_level' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 5],
                        'notes' => ['type' => 'string', 'maxLength' => 1000],
                    ],
                    'required' => ['duration_min'],
                    'additionalProperties' => false,
                ],
            ],
            [
                'name' => 'get_schedule',
                'description' => 'Use when the user asks about upcoming, active, or due feeding, walk, medication, or vet schedules.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'type' => ['type' => 'string', 'enum' => ['feeding', 'walk', 'medication', 'vet']],
                    ],
                    'additionalProperties' => false,
                ],
            ],
            [
                'name' => 'get_vet_history',
                'description' => 'Use when the user asks about the dog’s previous vet visits, diagnoses, or next due visit.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'since' => ['type' => 'string', 'format' => 'date'],
                        'limit' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 20],
                    ],
                    'additionalProperties' => false,
                ],
            ],
        ];
    }

    public function execute(string $name, array $input, User $user): array
    {
        $toolClass = self::TOOL_CLASSES[$name] ?? throw new InvalidArgumentException("Unknown tool: {$name}");

        return (new $toolClass($user))($input);
    }
}
