<?php

declare(strict_types=1);

namespace Tests\Feature\Llm;

use App\Models\Dog;
use App\Models\Feeding;
use App\Models\PlaySession;
use App\Models\User;
use App\Models\Walk;
use App\Services\AgentLoop;
use App\Services\Llm\LlmClient;
use App\Services\Llm\LlmResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class AgentLoopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.llm.driver' => 'fake']);
    }

    public function test_fake_client_logs_a_feeding(): void
    {
        [$user, $dog] = $this->userWithDog();

        $result = app(AgentLoop::class)->run($user, 'fed Buddy 200g kibble, ate most of it', dogId: $dog->id);

        $feeding = Feeding::query()->sole();
        $this->assertSame($dog->id, $feeding->dog_id);
        $this->assertSame('chat', $feeding->source);
        $this->assertSame(200, $feeding->amount_g);
        $this->assertSame('most', $feeding->portion_eaten);
        $this->assertNotSame('', $result->reply);
        $this->assertFalse($result->failed);
    }

    public function test_fake_client_logs_a_walk(): void
    {
        [$user, $dog] = $this->userWithDog();

        app(AgentLoop::class)->run($user, 'walked 45 minutes, 2.5 km', dogId: $dog->id);

        $walk = Walk::query()->sole();
        $this->assertSame($dog->id, $walk->dog_id);
        $this->assertSame(45, $walk->duration_min);
        $this->assertSame('2.50', $walk->distance_km);
        $this->assertSame('chat', $walk->source);
    }

    public function test_fake_client_logs_play(): void
    {
        [$user, $dog] = $this->userWithDog();

        app(AgentLoop::class)->run($user, 'played fetch for 20 minutes', dogId: $dog->id);

        $play = PlaySession::query()->sole();
        $this->assertSame($dog->id, $play->dog_id);
        $this->assertSame('fetch', $play->activity);
        $this->assertSame(20, $play->duration_min);
        $this->assertSame('chat', $play->source);
    }

    public function test_vet_lookup_returns_a_reply_without_creating_activity_rows(): void
    {
        [$user, $dog] = $this->userWithDog();

        $result = app(AgentLoop::class)->run($user, 'when was the last vet visit', dogId: $dog->id);

        $this->assertNotSame('', $result->reply);
        $this->assertFalse($result->failed);
        $this->assertSame(0, Feeding::count() + Walk::count() + PlaySession::count());
    }

    public function test_invalid_tool_input_is_returned_as_an_error_message(): void
    {
        [$user, $dog] = $this->userWithDog();

        $result = app(AgentLoop::class)->run($user, 'invalid walk', dogId: $dog->id);

        $this->assertStringStartsWith("That didn't work:", $result->reply);
        $this->assertSame(0, Walk::count());
    }

    public function test_unknown_tool_does_not_crash_or_create_rows(): void
    {
        [$user, $dog] = $this->userWithDog();

        $result = app(AgentLoop::class)->run($user, 'bad tool', dogId: $dog->id);

        $this->assertNotSame('', $result->reply);
        $this->assertSame(0, Feeding::count() + Walk::count() + PlaySession::count());
    }

    public function test_fake_outage_returns_friendly_failed_result(): void
    {
        [$user, $dog] = $this->userWithDog();

        $result = app(AgentLoop::class)->run($user, 'outage fail', dogId: $dog->id);

        $this->assertTrue($result->failed);
        $this->assertSame("Sorry, I couldn't reach the assistant. Please try again.", $result->reply);
        $this->assertSame(0, Feeding::count() + Walk::count() + PlaySession::count());
    }

    public function test_unrecognized_message_returns_the_generic_reply(): void
    {
        [$user, $dog] = $this->userWithDog();

        $result = app(AgentLoop::class)->run($user, 'hello there', dogId: $dog->id);

        $this->assertSame('I can log feedings, walks, and play sessions, and look up vet history and schedules.', $result->reply);
        $this->assertFalse($result->failed);
        $this->assertSame(0, Feeding::count() + Walk::count() + PlaySession::count());
    }

    public function test_fake_driver_cannot_be_resolved_in_production(): void
    {
        config(['services.llm.driver' => 'fake']);
        $this->app['env'] = 'production';

        try {
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage('fake LLM driver cannot be used in production');
            app(LlmClient::class);
        } finally {
            $this->app['env'] = 'testing';
        }
    }

    public function test_selected_buddy_receives_feeding_even_when_model_requests_milo(): void
    {
        [$user, $milo] = $this->userWithDog('Milo');
        $buddy = $user->dogs()->create(['name' => 'Buddy']);

        $this->app->instance(LlmClient::class, new class($milo->id) implements LlmClient
        {
            private int $calls = 0;

            public function __construct(private int $otherDogId) {}

            public function send(array $messages, array $tools, string $system): LlmResponse
            {
                $this->calls++;

                if ($this->calls === 1) {
                    return new LlmResponse(null, [[
                        'id' => 'test_call',
                        'name' => 'log_feeding',
                        'input' => ['food_type' => 'kibble', 'amount_g' => 200, 'dog_id' => $this->otherDogId],
                    ]], 'tool_use');
                }

                return new LlmResponse('Feeding logged.', [], 'end');
            }
        });

        $result = app(AgentLoop::class)->run($user, 'fed 200g kibble', dogId: $buddy->id);

        $feeding = Feeding::query()->sole();
        $this->assertSame($buddy->id, $feeding->dog_id);
        $this->assertNotSame($milo->id, $feeding->dog_id);
        $this->assertSame(200, $feeding->amount_g);
        $this->assertFalse($result->failed);
    }

    public function test_selected_dog_must_belong_to_the_user(): void
    {
        [$user] = $this->userWithDog();
        [, $otherDog] = $this->userWithDog('Other');

        $result = app(AgentLoop::class)->run($user, 'fed 200g kibble', dogId: $otherDog->id);

        $this->assertTrue($result->failed);
        $this->assertSame("I couldn't find that dog.", $result->reply);
        $this->assertSame(0, Feeding::count());
    }

    public function test_default_system_prompt_only_describes_the_resolved_dog(): void
    {
        [$user, $milo] = $this->userWithDog('Milo', 'Peanuts');
        $buddy = $user->dogs()->create(['name' => 'Buddy', 'allergies' => 'Chicken']);
        $client = new class implements LlmClient
        {
            public string $system = '';

            public function send(array $messages, array $tools, string $system): LlmResponse
            {
                $this->system = $system;

                return new LlmResponse('Done.', [], 'end');
            }
        };
        $this->app->instance(LlmClient::class, $client);

        app(AgentLoop::class)->run($user, 'hello', dogId: $buddy->id);

        $this->assertStringContainsString('Buddy', $client->system);
        $this->assertStringContainsString('Chicken', $client->system);
        $this->assertStringNotContainsString($milo->name, $client->system);
        $this->assertStringNotContainsString('Peanuts', $client->system);
    }

    /** @return array{User, Dog} */
    private function userWithDog(string $dogName = 'Buddy', ?string $allergies = null): array
    {
        $user = User::factory()->create();
        $dog = $user->dogs()->create(['name' => $dogName, 'allergies' => $allergies]);

        return [$user, $dog];
    }
}
