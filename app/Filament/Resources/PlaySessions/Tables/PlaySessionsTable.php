<?php

namespace App\Filament\Resources\PlaySessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlaySessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dog.name')
                    ->searchable(),
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
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
