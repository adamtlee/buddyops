<?php

namespace App\Filament\Pages;

use App\Models\ChatMessage;
use App\Models\User;
use App\Services\AgentLoop;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Throwable;

class Chat extends Page
{
    protected string $view = 'filament.pages.chat';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Chat';

    protected static ?int $navigationSort = -2;

    protected static ?string $title = 'Chat';

    public function getHeading(): string
    {
        return '';
    }

    public string $prompt = '';

    public ?int $dogId = null;

    /** @var array<int, array{role: string, content: string}> */
    public array $messages = [];

    public function mount(): void
    {
        $user = $this->currentUser();
        $this->dogId = $this->defaultDogId($user);
    }

    public function updatedDogId(): void
    {
        $user = $this->currentUser();

        if ($this->dogId !== null && ! $user->dogs()->whereKey($this->dogId)->exists()) {
            $this->dogId = $this->defaultDogId($user);
        }

        $this->loadMessages();
    }

    public function send(): void
    {
        $prompt = trim($this->prompt);

        if ($prompt === '') {
            return;
        }

        $user = $this->currentUser();

        if ($this->dogId !== null && ! $user->dogs()->whereKey($this->dogId)->exists()) {
            $this->dogId = $this->defaultDogId($user);
        }

        $history = ChatMessage::query()
            ->where('user_id', $user->id)
            ->where('dog_id', $this->dogId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get(['role', 'content', 'tool_calls', 'tool_call_id', 'is_error'])
            ->reverse()
            ->map(fn (ChatMessage $message): array => match ($message->role) {
                'assistant' => [
                    'role' => 'assistant',
                    'content' => $message->content,
                    'tool_calls' => $message->tool_calls ?? [],
                ],
                'tool' => [
                    'role' => 'tool',
                    'tool_call_id' => $message->tool_call_id ?? '',
                    'content' => $message->content ?? '',
                    'is_error' => $message->is_error,
                ],
                default => ['role' => 'user', 'content' => $message->content ?? ''],
            })
            ->values()
            ->all();

        try {
            $result = app(AgentLoop::class)->run($user, $prompt, $history, $this->dogId);

            if (! $result->failed) {
                DB::transaction(function () use ($user, $result): void {
                    foreach ($result->newMessages as $newMessage) {
                        ChatMessage::create([
                            'user_id' => $user->id,
                            'dog_id' => $this->dogId,
                            'role' => $newMessage['role'],
                            'content' => $newMessage['content'] ?? null,
                            'tool_calls' => $newMessage['tool_calls'] ?? null,
                            'tool_call_id' => $newMessage['tool_call_id'] ?? null,
                            'is_error' => $newMessage['is_error'] ?? false,
                        ]);
                    }
                });

                $reply = $result->reply;
                $isError = false;
            } else {
                $reply = $result->reply;
                $isError = true;
            }
        } catch (Throwable) {
            $reply = "Sorry, I couldn't reach the assistant. Please try again.";
            $isError = true;
        }

        $this->messages[] = ['role' => 'user', 'content' => $prompt];
        $this->messages[] = ['role' => 'assistant', 'content' => $reply, 'is_error' => $isError];
        $this->prompt = '';
    }

    private function currentUser(): User
    {
        $user = auth()->user();
        abort_unless($user instanceof User, 403);

        return $user;
    }

    private function defaultDogId(User $user): ?int
    {
        $buddyId = $user->dogs()->where('name', 'Buddy')->orderBy('id')->value('id');

        if ($buddyId !== null) {
            return (int) $buddyId;
        }

        $firstDogId = $user->dogs()->orderBy('id')->value('id');

        return $firstDogId === null ? null : (int) $firstDogId;
    }

    private function loadMessages(): void
    {
        $user = $this->currentUser();

        $this->messages = ChatMessage::query()
            ->where('user_id', $user->id)
            ->where('dog_id', $this->dogId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get(['role', 'content', 'is_error'])
            ->reverse()
            ->map(fn (ChatMessage $message): array => [
                'role' => $message->role,
                'content' => $message->content ?? '',
                'is_error' => $message->is_error,
            ])
            ->values()
            ->all();
    }
}
