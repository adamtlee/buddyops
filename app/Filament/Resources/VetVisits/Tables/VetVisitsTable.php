<?php

namespace App\Filament\Resources\VetVisits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VetVisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dog.name')
                    ->searchable(),
                TextColumn::make('visited_on')
                    ->date()
                    ->sortable(),
                TextColumn::make('clinic')
                    ->searchable(),
                TextColumn::make('reason')
                    ->searchable(),
                TextColumn::make('next_due_on')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('visited_on', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
