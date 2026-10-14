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

class FeedingsRelationManager extends RelationManager
{
    protected static string $relationship = 'feedings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('fed_at')
                    ->default(now())
                    ->required(),
                TextInput::make('food_type')
                    ->required(),
                TextInput::make('amount_g')
                    ->numeric()
                    ->suffix('g'),
                Select::make('portion_eaten')
                    ->options(['all' => 'All', 'most' => 'Most', 'some' => 'Some', 'none' => 'None'])
                    ->default('all')
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Hidden::make('source')
                    ->default('web'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fed_at')
            ->columns([
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
