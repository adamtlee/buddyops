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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HealthFlagsRelationManager extends RelationManager
{
    protected static string $relationship = 'healthFlags';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
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
