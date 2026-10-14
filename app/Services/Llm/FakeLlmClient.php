<?php

declare(strict_types=1);

namespace App\Services\Llm;

use RuntimeException;

final class FakeLlmClient implements LlmClient
{
    public function send(array $messages, array $tools, string $system): LlmResponse
    {
        $lastMessage = $messages === [] ? null : $messages[array_key_last($messages)];

        if (($lastMessage['role'] ?? null) === 'tool') {
            $decoded = json_decode((string) ($lastMessage['content'] ?? ''), true);
            $error = (bool) ($lastMessage['is_error'] ?? false);

            if ($error) {
                $errorText = is_array($decoded) && isset($decoded['message'])
                    ? (string) $decoded['message']
                    : (string) ($lastMessage['content'] ?? 'Unknown error');

                return new LlmResponse("That didn't work: {$errorText}", [], 'end');
            }

            $text = is_array($decoded) && isset($decoded['message'])
                ? (string) $decoded['message']
                : (string) ($lastMessage['content'] ?? '');

            return new LlmResponse($text, [], 'end');
        }

        $lastUserMessage = null;

        foreach (array_reverse($messages) as $message) {
            if (($message['role'] ?? null) === 'user') {
                $lastUserMessage = (string) ($message['content'] ?? '');
                break;
            }
        }

        $prompt = strtolower($lastUserMessage ?? '');

        if (str_contains($prompt, 'fail') || str_contains($prompt, 'outage')) {
            throw new RuntimeException('Simulated LLM outage');
        }

        if (str_contains($prompt, 'bad tool')) {
            return $this->toolCall('nonexistent_tool', []);
        }

        if (str_contains($prompt, 'invalid')) {
            return $this->toolCall('log_walk', ['duration_min' => 0]);
        }

        if (str_contains($prompt, 'vet') || str_contains($prompt, 'checkup')) {
            return $this->toolCall('get_vet_history', []);
        }

        if (str_contains($prompt, 'schedule') || str_contains($prompt, 'due')) {
            return $this->toolCall('get_schedule', []);
        }

        if (str_contains($prompt, 'walk')) {
            preg_match('/\b(\d+)\b/', $prompt, $durationMatch);
            preg_match('/\b(\d+(?:\.\d+)?)\s*km\b/', $prompt, $distanceMatch);

            $input = ['duration_min' => isset($durationMatch[1]) ? (int) $durationMatch[1] : 30];

            if (isset($distanceMatch[1])) {
                $input['distance_km'] = (float) $distanceMatch[1];
            }

            return $this->toolCall('log_walk', $input);
        }

        if (str_contains($prompt, 'play') || str_contains($prompt, 'fetch') || str_contains($prompt, 'tug')) {
            preg_match('/\b(\d+)\b/', $prompt, $durationMatch);
            $activity = str_contains($prompt, 'tug')
                ? 'tug'
                : (str_contains($prompt, 'train') ? 'training' : 'fetch');

            return $this->toolCall('log_play', [
                'duration_min' => isset($durationMatch[1]) ? (int) $durationMatch[1] : 15,
                'activity' => $activity,
            ]);
        }

        if (
            str_contains($prompt, 'fed')
            || str_contains($prompt, 'ate')
            || str_contains($prompt, 'feed')
            || str_contains($prompt, 'dinner')
            || str_contains($prompt, 'breakfast')
        ) {
            $input = [
                'food_type' => str_contains($prompt, 'chicken') ? 'chicken' : 'kibble',
                'portion_eaten' => $this->portionEaten($prompt),
            ];

            if (preg_match('/\b(\d+)\s*(?:g|grams)\b/', $prompt, $amountMatch) === 1) {
                $input['amount_g'] = (int) $amountMatch[1];
            }

            return $this->toolCall('log_feeding', $input);
        }

        return new LlmResponse(
            'I can log feedings, walks, and play sessions, and look up vet history and schedules.',
            [],
            'end',
        );
    }

    private function toolCall(string $name, array $input): LlmResponse
    {
        return new LlmResponse(null, [[
            'id' => 'fake_'.uniqid(),
            'name' => $name,
            'input' => $input,
        ]], 'tool_use');
    }

    private function portionEaten(string $prompt): string
    {
        if (
            str_contains($prompt, 'refused')
            || str_contains($prompt, "didn't eat")
            || str_contains($prompt, 'did not eat')
        ) {
            return 'none';
        }

        if (str_contains($prompt, 'some')) {
            return 'some';
        }

        if (str_contains($prompt, 'most')) {
            return 'most';
        }

        return 'all';
    }
}
