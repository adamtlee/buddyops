<?php

namespace App\Filament\Resources\Walks;

use App\Filament\Resources\Walks\Pages\CreateWalk;
use App\Filament\Resources\Walks\Pages\EditWalk;
use App\Filament\Resources\Walks\Pages\ListWalks;
use App\Filament\Resources\Walks\Schemas\WalkForm;
use App\Filament\Resources\Walks\Tables\WalksTable;
use App\Models\Walk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WalkResource extends Resource
{
    protected static ?string $model = Walk::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?string $recordTitleAttribute = 'started_at';

    public static function form(Schema $schema): Schema
    {
        return WalkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WalksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWalks::route('/'),
            'create' => CreateWalk::route('/create'),
            'edit' => EditWalk::route('/{record}/edit'),
        ];
    }
}
