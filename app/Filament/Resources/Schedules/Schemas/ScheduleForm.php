<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dog_id')
                    ->relationship('dog', 'name')
                    ->required(),
                Select::make('type')
                    ->options(['feeding' => 'Feeding', 'walk' => 'Walk', 'medication' => 'Medication', 'vet' => 'Vet'])
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TimePicker::make('time_of_day')
                    ->required(),
                CheckboxList::make('days_of_week')
                    ->options([0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'])
                    ->columns(4)
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
