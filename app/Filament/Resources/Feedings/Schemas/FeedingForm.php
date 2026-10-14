<?php

namespace App\Filament\Resources\Feedings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class FeedingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dog_id')
                    ->relationship('dog', 'name')
                    ->required(),
                DateTimePicker::make('fed_at')
                    ->default(now())
                    ->required(),
                TextInput::make('food_type')
                    ->required(),
                TextInput::make('amount_g')
                    ->numeric()
                    ->suffix('g'),
                Select::make('portion_eaten')
                    ->options(['all' => 'All', 'most' => 'Most', 'some' => 'Some', 'none' => 'None'])
                    ->default('all')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Hidden::make('source')
                    ->default('web'),
            ]);
    }
}
