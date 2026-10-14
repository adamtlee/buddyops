<?php

declare(strict_types=1);

namespace App\Services;

final readonly class AgentResult
{
    /**
     * @param array<int, array<string, mixed>> $newMessages
     */
    public function __construct(
        public string $reply,
        public array $newMessages,
        public bool $failed,
    ) {}
}
