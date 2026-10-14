<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\QuickLog;
use App\Models\Dog;
use App\Models\Feeding;
use App\Models\PlaySession;
use App\Models\User;
use App\Models\Walk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuickLogTest extends TestCase
{
    use RefreshDatabase;

    private function userWithDog(): array
    {
        $user = User::factory()->create();
        $dog = $user->dogs()->create(['name' => 'Rex']);
        $this->actingAs($user);

        return [$user, $dog];
    }

    public function test_page_loads_for_user_with_a_dog(): void
    {
        [, $dog] = $this->userWithDog();

        Livewire::test(QuickLog::class)
            ->assertOk()
            ->assertSet('selectedDogId', $dog->id);
    }

    public function test_fed_action_creates_a_web_feeding_for_the_dog(): void
    {
        [, $dog] = $this->userWithDog();

        Livewire::test(QuickLog::class)
            ->callAction('fed', ['food_type' => 'kibble', 'portion_eaten' => 'all'])
            ->assertHasNoActionErrors();

        $feeding = Feeding::first();
        $this->assertSame($dog->id, $feeding->dog_id);
        $this->assertSame('web', $feeding->source);
    }

    public function test_walked_and_played_actions_each_create_one_row(): void
    {
        $this->userWithDog();

        Livewire::test(QuickLog::class)
            ->callAction('walked', ['duration_min' => 30])
            ->callAction('played', ['activity' => 'fetch', 'duration_min' => 15]);

        $this->assertSame(1, Walk::count());
        $this->assertSame(1, PlaySession::count());
    }

    public function test_invalid_input_creates_no_row_and_shows_error_notification(): void
    {
        $this->userWithDog();

        Livewire::test(QuickLog::class)
            ->callAction('walked', ['duration_min' => 0])
            ->assertHasActionErrors(['duration_min']);

        Livewire::test(QuickLog::class)
            ->callAction('walked', ['duration_min' => 601])
            ->assertNotified('Could not log activity');

        $this->assertSame(0, Walk::count());
    }

    public function test_user_cannot_log_against_another_users_dog(): void
    {
        $this->userWithDog();
        $otherDog = User::factory()->create()->dogs()->create(['name' => 'Other']);

        Livewire::test(QuickLog::class)
            ->set('selectedDogId', $otherDog->id)
            ->callAction('fed', ['food_type' => 'kibble'])
            ->assertNotified('Dog not found');

        $this->assertSame(0, Feeding::count());
    }
}
