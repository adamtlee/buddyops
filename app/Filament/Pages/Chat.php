<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

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

    /** @var array<int, array{role: string, content: string}> */
    public array $messages = [];

    public function send(): void
    {
        $prompt = trim($this->prompt);

        if ($prompt === '') {
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $prompt];
        $this->messages[] = ['role' => 'assistant', 'content' => 'This is a test response. No LLM is configured yet.'];
        $this->prompt = '';
    }
}
