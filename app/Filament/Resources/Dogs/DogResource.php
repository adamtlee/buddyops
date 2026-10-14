<?php

namespace App\Filament\Resources\Dogs;

use App\Filament\Resources\Dogs\Pages\CreateDog;
use App\Filament\Resources\Dogs\Pages\EditDog;
use App\Filament\Resources\Dogs\Pages\ListDogs;
use App\Filament\Resources\Dogs\Pages\ViewDog;
use App\Filament\Resources\Dogs\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Dogs\RelationManagers\FeedingsRelationManager;
use App\Filament\Resources\Dogs\RelationManagers\HealthFlagsRelationManager;
use App\Filament\Resources\Dogs\RelationManagers\PlaySessionsRelationManager;
use App\Filament\Resources\Dogs\RelationManagers\SchedulesRelationManager;
use App\Filament\Resources\Dogs\RelationManagers\VetVisitsRelationManager;
use App\Filament\Resources\Dogs\RelationManagers\WalksRelationManager;
use App\Filament\Resources\Dogs\Schemas\DogForm;
use App\Filament\Resources\Dogs\Schemas\DogInfolist;
use App\Filament\Resources\Dogs\Tables\DogsTable;
use App\Models\Dog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DogResource extends Resource
{
    protected static ?string $model = Dog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DogForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FeedingsRelationManager::class,
            WalksRelationManager::class,
            PlaySessionsRelationManager::class,
            VetVisitsRelationManager::class,
            HealthFlagsRelationManager::class,
            SchedulesRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDogs::route('/'),
            'create' => CreateDog::route('/create'),
            'view' => ViewDog::route('/{record}'),
            'edit' => EditDog::route('/{record}/edit'),
        ];
    }
}
