<?php

namespace App\Filament\Resources\VetVisits\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VetVisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dog_id')
                    ->relationship('dog', 'name')
                    ->required(),
                DatePicker::make('visited_on')
                    ->default(today())
                    ->required(),
                TextInput::make('clinic'),
                TextInput::make('reason')
                    ->required(),
                Textarea::make('diagnosis')
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DatePicker::make('next_due_on'),
            ]);
    }
}
