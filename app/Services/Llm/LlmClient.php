<?php

declare(strict_types=1);

namespace App\Services\Llm;

interface LlmClient
{
    public function send(array $messages, array $tools, string $system): LlmResponse;
}
