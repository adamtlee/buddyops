<?php

namespace App\Filament\Resources\Walks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WalkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dog_id')
                    ->relationship('dog', 'name')
                    ->required(),
                DateTimePicker::make('started_at')
                    ->default(now())
                    ->required(),
                TextInput::make('duration_min')
                    ->numeric()
                    ->minValue(1)
                    ->suffix('min')
                    ->required(),
                TextInput::make('distance_km')
                    ->numeric()
                    ->suffix('km'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Hidden::make('source')
                    ->default('web'),
            ]);
    }
}
