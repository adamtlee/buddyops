<?php

declare(strict_types=1);

namespace App\Services\Llm;

final readonly class LlmResponse
{
    /**
     * @param array<int, array{id: string, name: string, input: array}> $toolCalls
     */
    public function __construct(
        public ?string $text,
        public array $toolCalls,
        public string $stopReason,
    ) {}
}
