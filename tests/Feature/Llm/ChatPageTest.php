<?php

declare(strict_types=1);

namespace Tests\Feature\Llm;

use App\Filament\Pages\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.llm.driver' => 'fake']);
    }

    public function test_sending_message_persists_the_successful_exchange_for_selected_user_and_dog(): void
    {
        $user = User::factory()->create();
        $dog = $user->dogs()->create(['name' => 'Buddy']);
        $this->actingAs($user);

        Livewire::test(Chat::class)
            ->assertSet('dogId', $dog->id)
            ->set('prompt', 'fed 200g kibble')
            ->call('send')
            ->assertSet('prompt', '')
            ->assertSee('Logged feeding for Buddy');

        $this->assertSame(4, ChatMessage::count());
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'dog_id' => $dog->id,
            'role' => 'user',
            'content' => 'fed 200g kibble',
        ]);
        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'dog_id' => $dog->id,
            'role' => 'tool',
        ]);
    }

    public function test_failed_send_displays_error_and_does_not_persist_exchange(): void
    {
        $user = User::factory()->create();
        $dog = $user->dogs()->create(['name' => 'Buddy']);
        $this->actingAs($user);

        Livewire::test(Chat::class)
            ->set('prompt', 'outage fail')
            ->call('send')
            ->assertSet('prompt', '')
            ->assertSee("Sorry, I couldn't reach the assistant. Please try again.");

        $this->assertSame(0, ChatMessage::count());
    }
}
