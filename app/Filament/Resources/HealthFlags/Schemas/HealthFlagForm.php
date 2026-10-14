<?php

namespace App\Filament\Resources\HealthFlags\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class HealthFlagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dog_id')
                    ->relationship('dog', 'name')
                    ->required(),
                DateTimePicker::make('flagged_at')
                    ->default(now())
                    ->required(),
                TextInput::make('description')
                    ->required(),
                Select::make('severity')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->default('low')
                    ->required(),
                Select::make('status')
                    ->options(['open' => 'Open', 'sent_to_vet' => 'Sent to vet', 'resolved' => 'Resolved'])
                    ->default('open')
                    ->required(),
                Hidden::make('created_by')
                    ->default('user'),
                DateTimePicker::make('resolved_at'),
            ]);
    }
}
