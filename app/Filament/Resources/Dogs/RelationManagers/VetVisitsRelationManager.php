<?php

namespace App\Filament\Resources\Dogs\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VetVisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'vetVisits';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visited_on')
            ->columns([
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
