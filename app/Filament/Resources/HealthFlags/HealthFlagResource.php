<?php

namespace App\Filament\Resources\HealthFlags;

use App\Filament\Resources\HealthFlags\Pages\CreateHealthFlag;
use App\Filament\Resources\HealthFlags\Pages\EditHealthFlag;
use App\Filament\Resources\HealthFlags\Pages\ListHealthFlags;
use App\Filament\Resources\HealthFlags\Schemas\HealthFlagForm;
use App\Filament\Resources\HealthFlags\Tables\HealthFlagsTable;
use App\Models\HealthFlag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HealthFlagResource extends Resource
{
    protected static ?string $model = HealthFlag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return HealthFlagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HealthFlagsTable::configure($table);
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
            'index' => ListHealthFlags::route('/'),
            'create' => CreateHealthFlag::route('/create'),
            'edit' => EditHealthFlag::route('/{record}/edit'),
        ];
    }
}
