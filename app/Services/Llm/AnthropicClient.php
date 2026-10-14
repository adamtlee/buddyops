<?php

declare(strict_types=1);

namespace App\Services\Llm;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class AnthropicClient implements LlmClient
{
    public function send(array $messages, array $tools, string $system): LlmResponse
    {
        $key = config('services.anthropic.key');

        if (! is_string($key) || trim($key) === '') {
            throw new RuntimeException('The Anthropic API key is missing. Set ANTHROPIC_API_KEY to use the Anthropic driver.');
        }

        $payload = [
            'model' => config('services.anthropic.model'),
            'max_tokens' => 1024,
            'system' => $system,
            'messages' => $this->toAnthropicMessages($messages),
        ];

        if ($tools !== []) {
            $payload['tools'] = $tools;
        }

        $response = $this->postWithRetries($key, $payload);
        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException('Anthropic returned an invalid response.');
        }

        $textParts = [];
        $toolCalls = [];

        foreach ($data['content'] ?? [] as $block) {
            if (! is_array($block)) {
                continue;
            }

            if (($block['type'] ?? null) === 'text' && isset($block['text'])) {
                $textParts[] = (string) $block['text'];
            } elseif (($block['type'] ?? null) === 'tool_use') {
                $toolCalls[] = [
                    'id' => (string) ($block['id'] ?? ''),
                    'name' => (string) ($block['name'] ?? ''),
                    'input' => is_array($block['input'] ?? null) ? $block['input'] : [],
                ];
            }
        }

        return new LlmResponse(
            $textParts === [] ? null : implode("\n", $textParts),
            $toolCalls,
            ($data['stop_reason'] ?? null) === 'tool_use' ? 'tool_use' : 'end',
        );
    }

    private function postWithRetries(string $key, array $payload): Response
    {
        for ($attempt = 0; $attempt < 3; $attempt++) {
            $response = Http::withHeaders([
                'x-api-key' => $key,
                'anthropic-version' => '2023-06-01',
            ])
                ->timeout(60)
                ->post('https://api.anthropic.com/v1/messages', $payload);

            if (($response->status() === 429 || $response->serverError()) && $attempt < 2) {
                continue;
            }

            return $response->throw();
        }

        throw new RuntimeException('Unable to reach Anthropic after retries.');
    }

    private function toAnthropicMessages(array $messages): array
    {
        $translated = [];
        $toolResults = [];

        $flushToolResults = static function () use (&$translated, &$toolResults): void {
            if ($toolResults === []) {
                return;
            }

            $translated[] = ['role' => 'user', 'content' => $toolResults];
            $toolResults = [];
        };

        foreach ($messages as $message) {
            if (! is_array($message)) {
                continue;
            }

            $role = $message['role'] ?? null;

            if ($role === 'tool') {
                $toolResults[] = [
                    'type' => 'tool_result',
                    'tool_use_id' => (string) ($message['tool_call_id'] ?? ''),
                    'content' => (string) ($message['content'] ?? ''),
                    'is_error' => (bool) ($message['is_error'] ?? false),
                ];

                continue;
            }

            $flushToolResults();

            if ($role === 'user') {
                $translated[] = [
                    'role' => 'user',
                    'content' => (string) ($message['content'] ?? ''),
                ];

                continue;
            }

            if ($role === 'assistant') {
                $blocks = [];

                if (isset($message['content']) && $message['content'] !== '') {
                    $blocks[] = ['type' => 'text', 'text' => (string) $message['content']];
                }

                foreach ($message['tool_calls'] ?? [] as $toolCall) {
                    if (! is_array($toolCall)) {
                        continue;
                    }

                    $blocks[] = [
                        'type' => 'tool_use',
                        'id' => (string) ($toolCall['id'] ?? ''),
                        'name' => (string) ($toolCall['name'] ?? ''),
                        'input' => is_array($toolCall['input'] ?? null) ? $toolCall['input'] : [],
                    ];
                }

                if ($blocks !== []) {
                    $translated[] = ['role' => 'assistant', 'content' => $blocks];
                }
            }
        }

        $flushToolResults();

        return $translated;
    }
}
