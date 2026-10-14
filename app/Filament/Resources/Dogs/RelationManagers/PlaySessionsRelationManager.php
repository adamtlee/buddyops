<?php

namespace App\Filament\Resources\Dogs\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlaySessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'playSessions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('played_at')
            ->columns([
                TextColumn::make('played_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('activity'),
                TextColumn::make('duration_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('energy_level')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('played_at', 'desc')
            ->filters([
                SelectFilter::make('activity')
                    ->options(['fetch' => 'Fetch', 'tug' => 'Tug', 'training' => 'Training', 'other' => 'Other']),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
