<?php

declare(strict_types=1);

namespace Tests\Feature\Llm;

use App\Services\Llm\AnthropicClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnthropicClientTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.anthropic.key' => 'test-api-key',
            'services.anthropic.model' => 'claude-haiku-5-5',
        ]);
    }

    public function test_plain_text_response_is_translated(): void
    {
        Http::fake([
            'api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => 'Hello from Anthropic.']],
                'stop_reason' => 'end_turn',
            ]),
        ]);

        $response = (new AnthropicClient())->send(
            [['role' => 'user', 'content' => 'Hello']],
            [],
            'System prompt',
        );

        $this->assertSame('Hello from Anthropic.', $response->text);
        $this->assertSame([], $response->toolCalls);
        $this->assertSame('end', $response->stopReason);

        Http::assertSent(fn ($request): bool =>
            $request->url() === 'https://api.anthropic.com/v1/messages'
            && $request->hasHeader('x-api-key', 'test-api-key')
            && $request->hasHeader('anthropic-version', '2023-06-01')
            && $request['max_tokens'] === 1024
            && $request['system'] === 'System prompt'
        );
    }

    public function test_tool_use_and_follow_up_final_reply_are_translated(): void
    {
        Http::fakeSequence()
            ->push([
                'content' => [[
                    'type' => 'tool_use',
                    'id' => 'call_1',
                    'name' => 'get_vet_history',
                    'input' => [],
                ]],
                'stop_reason' => 'tool_use',
            ])
            ->push([
                'content' => [['type' => 'text', 'text' => 'No vet visits found.']],
                'stop_reason' => 'end_turn',
            ]);

        $client = new AnthropicClient();
        $tools = [[
            'name' => 'get_vet_history',
            'description' => 'Look up vet history.',
            'input_schema' => ['type' => 'object', 'properties' => []],
        ]];
        $first = $client->send([['role' => 'user', 'content' => 'Vet history?']], $tools, 'system');

        $this->assertSame('tool_use', $first->stopReason);
        $this->assertSame([[
            'id' => 'call_1',
            'name' => 'get_vet_history',
            'input' => [],
        ]], $first->toolCalls);

        $second = $client->send([
            ['role' => 'user', 'content' => 'Vet history?'],
            ['role' => 'assistant', 'content' => null, 'tool_calls' => $first->toolCalls],
            ['role' => 'tool', 'tool_call_id' => 'call_1', 'content' => '{"visits":[]}', 'is_error' => false],
        ], $tools, 'system');

        $this->assertSame('No vet visits found.', $second->text);
        $this->assertSame('end', $second->stopReason);

        Http::assertSentCount(2);
        Http::assertSent(fn ($request): bool =>
            isset($request['messages'][1]['content'][0])
            && $request['messages'][1]['content'][0]['type'] === 'tool_use'
            && $request['messages'][2]['role'] === 'user'
            && $request['messages'][2]['content'][0]['type'] === 'tool_result'
            && $request['messages'][2]['content'][0]['is_error'] === false
        );
    }
}
