<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Dog;
use App\Models\User;
use App\Services\Llm\LlmClient;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

final class AgentLoop
{
    public function __construct(
        private readonly LlmClient $client,
        private readonly ToolRegistry $tools,
    ) {}

    public function run(User $user, string $message, array $history = [], ?int $dogId = null): AgentResult
    {
        $newMessages = [['role' => 'user', 'content' => $message]];
        $dog = $dogId !== null
            ? $user->dogs()->find($dogId)
            : $user->dogs()->orderBy('id')->first();

        if ($dogId !== null && ! $dog instanceof Dog) {
            return new AgentResult("I couldn't find that dog.", $newMessages, true);
        }

        $system = $this->systemPrompt($dog);
        $schemas = $this->tools->schemas();

        try {
            for ($iteration = 0; $iteration < 5; $iteration++) {
                $response = $this->client->send(
                    array_merge($history, $newMessages),
                    $schemas,
                    $system,
                );

                if ($response->toolCalls === []) {
                    $reply = $response->text ?? '';
                    $newMessages[] = [
                        'role' => 'assistant',
                        'content' => $reply,
                        'tool_calls' => [],
                    ];

                    return new AgentResult($reply, $newMessages, false);
                }

                $newMessages[] = [
                    'role' => 'assistant',
                    'content' => $response->text,
                    'tool_calls' => $response->toolCalls,
                ];

                foreach ($response->toolCalls as $toolCall) {
                    $input = is_array($toolCall['input'] ?? null) ? $toolCall['input'] : [];

                    if ($dog instanceof Dog) {
                        $input['dog_id'] = $dog->id;
                    }

                    if (in_array($toolCall['name'] ?? '', ['log_feeding', 'log_walk', 'log_play'], true)) {
                        $input['source'] = 'chat';
                    }

                    try {
                        $result = $this->tools->execute((string) ($toolCall['name'] ?? ''), $input, $user);
                        $isError = false;
                    } catch (ValidationException $exception) {
                        $result = ['message' => collect($exception->errors())->flatten()->implode(' ')];
                        $isError = true;
                    } catch (ModelNotFoundException) {
                        $result = ['message' => "I couldn't find the selected dog or requested record."];
                        $isError = true;
                    } catch (InvalidArgumentException $exception) {
                        $result = ['message' => $exception->getMessage()];
                        $isError = true;
                    }

                    $newMessages[] = [
                        'role' => 'tool',
                        'tool_call_id' => (string) ($toolCall['id'] ?? ''),
                        'content' => json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'is_error' => $isError,
                    ];
                }
            }
        } catch (Throwable) {
            return new AgentResult("Sorry, I couldn't reach the assistant. Please try again.", $newMessages, true);
        }

        return new AgentResult('I got stuck on that. Please try rephrasing.', $newMessages, true);
    }

    private function systemPrompt(?Dog $dog): string
    {
        $timezone = (string) config('app.timezone');
        $today = now($timezone)->toDateString();
        $dogDetails = $dog instanceof Dog
            ? "Selected dog: {$dog->name}. Allergies: ".($dog->allergies ?: 'none recorded').'.'
            : 'No dog is currently associated with this account.';

        return "Today is {$today}. The application timezone is {$timezone}.\n"
            .$dogDetails."\n"
            .'Use the available tools to log activities and look up this selected dog’s information. '
            .'Never use information about any other dog, and never give medical advice. For medical concerns, recommend contacting a veterinarian.';
    }
}
