<?php

namespace App\Filament\Resources\PlaySessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PlaySessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dog_id')
                    ->relationship('dog', 'name')
                    ->required(),
                DateTimePicker::make('played_at')
                    ->default(now())
                    ->required(),
                Select::make('activity')
                    ->options(['fetch' => 'Fetch', 'tug' => 'Tug', 'training' => 'Training', 'other' => 'Other'])
                    ->default('other')
                    ->required(),
                TextInput::make('duration_min')
                    ->numeric()
                    ->minValue(1)
                    ->suffix('min')
                    ->required(),
                Select::make('energy_level')
                    ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5']),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Hidden::make('source')
                    ->default('web'),
            ]);
    }
}
