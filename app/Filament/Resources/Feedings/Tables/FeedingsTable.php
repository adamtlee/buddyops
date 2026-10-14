<?php

namespace App\Filament\Resources\Feedings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FeedingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dog.name')
                    ->searchable(),
                TextColumn::make('fed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('food_type')
                    ->searchable(),
                TextColumn::make('amount_g')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('portion_eaten')
                    ->badge(),
            ])
            ->defaultSort('fed_at', 'desc')
            ->filters([
                SelectFilter::make('portion_eaten')
                    ->options(['all' => 'All', 'most' => 'Most', 'some' => 'Some', 'none' => 'None']),
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
