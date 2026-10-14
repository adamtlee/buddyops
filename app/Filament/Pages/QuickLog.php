<?php

namespace App\Filament\Pages;

use App\Tools\LogFeeding;
use App\Tools\LogPlay;
use App\Tools\LogWalk;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;

class QuickLog extends Page
{
    protected string $view = 'filament.pages.quick-log';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = 'Quick Log';

    protected static ?int $navigationSort = -2;

    protected static ?string $title = 'Quick Log';

    public ?int $selectedDogId = null;

    public function mount(): void
    {
        $this->selectedDogId = auth()->user()->dogs()->orderBy('id')->value('id');
    }

    #[Computed]
    public function dogs(): Collection
    {
        return auth()->user()->dogs()->orderBy('id')->pluck('name', 'id');
    }

    #[Computed]
    public function recentActivity(): Collection
    {
        $dog = auth()->user()->dogs()->whereKey($this->selectedDogId)->first();

        if (! $dog) {
            return collect();
        }

        $feedings = $dog->feedings()->latest('fed_at')->limit(10)->get()->map(fn ($f) => [
            'icon' => 'heroicon-o-cake',
            'text' => 'Fed: '.$f->food_type.($f->portion_eaten ? ', ate '.$f->portion_eaten : ''),
            'at' => $f->fed_at,
        ]);

        $walks = $dog->walks()->latest('started_at')->limit(10)->get()->map(fn ($w) => [
            'icon' => 'heroicon-o-map-pin',
            'text' => 'Walk: '.$w->duration_min.' min'.($w->distance_km ? ', '.$w->distance_km.' km' : ''),
            'at' => $w->started_at,
        ]);

        $plays = $dog->playSessions()->latest('played_at')->limit(10)->get()->map(fn ($p) => [
            'icon' => 'heroicon-o-trophy',
            'text' => 'Play: '.$p->activity.', '.$p->duration_min.' min',
            'at' => $p->played_at,
        ]);

        return $feedings->concat($walks)->concat($plays)->sortByDesc('at')->take(10)->values();
    }

    public function fedAction(): Action
    {
        return Action::make('fed')
            ->label('Fed')
            ->icon(Heroicon::OutlinedCake)
            ->color('success')
            ->size('xl')
            ->extraAttributes(['class' => 'w-full'])
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Log it')
            ->schema([
                TextInput::make('food_type')
                    ->required()
                    ->default(fn () => auth()->user()->dogs()->whereKey($this->selectedDogId)->first()
                        ?->feedings()->latest('fed_at')->value('food_type') ?? 'kibble'),
                TextInput::make('amount_g')->numeric()->integer()->suffix('g'),
                Select::make('portion_eaten')
                    ->options(['all' => 'All', 'most' => 'Most', 'some' => 'Some', 'none' => 'None'])
                    ->default('all'),
                Textarea::make('notes'),
                DateTimePicker::make('fed_at')->default(now()),
            ])
            ->action(fn (array $data) => $this->runTool(LogFeeding::class, $data));
    }

    public function walkedAction(): Action
    {
        return Action::make('walked')
            ->label('Walked')
            ->icon(Heroicon::OutlinedMapPin)
            ->color('info')
            ->size('xl')
            ->extraAttributes(['class' => 'w-full'])
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Log it')
            ->schema([
                TextInput::make('duration_min')->numeric()->integer()->required()->default(30)->minValue(1)->suffix('min'),
                TextInput::make('distance_km')->numeric()->suffix('km'),
                Textarea::make('notes'),
                DateTimePicker::make('started_at')->default(now()),
            ])
            ->action(fn (array $data) => $this->runTool(LogWalk::class, $data));
    }

    public function playedAction(): Action
    {
        return Action::make('played')
            ->label('Played')
            ->icon(Heroicon::OutlinedTrophy)
            ->color('warning')
            ->size('xl')
            ->extraAttributes(['class' => 'w-full'])
            ->modalWidth(Width::Medium)
            ->modalSubmitActionLabel('Log it')
            ->schema([
                Select::make('activity')
                    ->options(['fetch' => 'Fetch', 'tug' => 'Tug', 'training' => 'Training', 'other' => 'Other'])
                    ->default('fetch'),
                TextInput::make('duration_min')->numeric()->integer()->required()->default(15)->suffix('min'),
                Select::make('energy_level')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                Textarea::make('notes'),
                DateTimePicker::make('played_at')->default(now()),
            ])
            ->action(fn (array $data) => $this->runTool(LogPlay::class, $data));
    }

    protected function runTool(string $toolClass, array $data): void
    {
        $input = array_filter($data, fn ($value) => $value !== null && $value !== '');
        $input['dog_id'] = $this->selectedDogId;
        $input['source'] = 'web';

        try {
            $result = (new $toolClass(auth()->user()))($input);
        } catch (ValidationException $e) {
            Notification::make()
                ->danger()
                ->title('Could not log activity')
                ->body(collect($e->errors())->flatten()->implode("\n"))
                ->send();

            return;
        } catch (ModelNotFoundException) {
            Notification::make()->danger()->title('Dog not found')->send();

            return;
        }

        Notification::make()->success()->title($result['message'])->send();

        unset($this->recentActivity);
    }
}
