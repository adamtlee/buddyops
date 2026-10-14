<?php

namespace Tests\Feature;

use App\Models\Dog;
use App\Models\Feeding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_endpoints_reject_requests_without_a_token(): void
    {
        $this->getJson('/api/v1/ping')->assertUnauthorized();
        $this->getJson('/api/v1/dogs')->assertUnauthorized();
    }

    public function test_admin_web_session_can_access_api_from_a_stateful_origin(): void
    {
        $user = User::factory()->create();
        $dog = Dog::factory()->for($user)->create();

        $this->actingAs($user, 'web')
            ->withHeader('Referer', 'http://localhost:8000/admin')
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);

        $this->app['auth']->forgetGuards();

        $this->actingAs($user, 'web')
            ->withHeader('Referer', 'http://localhost:8000/admin')
            ->getJson('/api/v1/dogs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $dog->id);
    }

    public function test_valid_credentials_issue_a_bearer_token_for_the_authorized_ping(): void
    {
        $user = User::factory()->create([
            'email' => 'api-user@example.com',
            'password' => 'correct-password',
        ]);

        $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'incorrect-password',
            'device_name' => 'feature-test',
        ])->assertUnauthorized();

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'correct-password',
            'device_name' => 'feature-test',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonMissingPath('data.user.password');

        $token = $response->json('data.access_token');

        $this->withToken($token)
            ->getJson('/api/v1/ping')
            ->assertOk()
            ->assertJsonPath('data.status', 'ok')
            ->assertJsonPath('data.user_id', $user->id);
    }

    public function test_tokens_without_the_read_ability_cannot_access_read_endpoints(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('write-only-client', ['api:write'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/ping')
            ->assertForbidden();
    }

    public function test_api_lists_only_the_authenticated_users_dogs_and_activity(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $dog = Dog::factory()->for($user)->create();
        $otherDog = Dog::factory()->for($otherUser)->create();
        Feeding::factory()->for($dog)->create();

        $token = $user->createToken('feature-test', ['api:read'])->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/v1/dogs')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $dog->id);

        $this->getJson("/api/v1/dogs/{$dog->id}/feedings")
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->getJson("/api/v1/dogs/{$otherDog->id}")->assertNotFound();
        $this->getJson("/api/v1/dogs/{$otherDog->id}/feedings")->assertNotFound();
    }

    public function test_current_token_can_be_revoked(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('feature-test', ['api:read']);

        $this->withToken($token->plainTextToken)
            ->deleteJson('/api/v1/auth/token')
            ->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->getKey(),
        ]);

        $this->app['auth']->forgetGuards();

        $this->withToken($token->plainTextToken)
            ->getJson('/api/v1/ping')
            ->assertUnauthorized();
    }
}
