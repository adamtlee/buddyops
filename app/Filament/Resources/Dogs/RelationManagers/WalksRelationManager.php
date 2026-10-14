<?php

namespace App\Filament\Resources\Dogs\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalksRelationManager extends RelationManager
{
    protected static string $relationship = 'walks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('started_at')
                    ->default(now())
                    ->required(),
                TextInput::make('duration_min')
                    ->numeric()
                    ->minValue(1)
                    ->suffix('min')
                    ->required(),
                TextInput::make('distance_km')
                    ->numeric()
                    ->suffix('km'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Hidden::make('source')
                    ->default('web'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('started_at')
            ->columns([
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duration_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('distance_km')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('started_at', 'desc')
            ->filters([
                //
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
