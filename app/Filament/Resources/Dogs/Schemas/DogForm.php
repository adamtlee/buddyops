<?php

namespace App\Filament\Resources\Dogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('breed'),
                Select::make('sex')
                    ->options(['male' => 'Male', 'female' => 'Female']),
                DatePicker::make('birthdate'),
                TextInput::make('weight_kg')
                    ->numeric()
                    ->suffix('kg'),
                Textarea::make('allergies')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
