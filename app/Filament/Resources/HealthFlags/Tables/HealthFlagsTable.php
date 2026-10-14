<?php

namespace App\Filament\Resources\HealthFlags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HealthFlagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dog.name')
                    ->searchable(),
                TextColumn::make('flagged_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->defaultSort('flagged_at', 'desc')
            ->filters([
                SelectFilter::make('severity')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']),
                SelectFilter::make('status')
                    ->options(['open' => 'Open', 'sent_to_vet' => 'Sent to vet', 'resolved' => 'Resolved']),
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
