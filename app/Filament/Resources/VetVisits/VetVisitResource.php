<?php

namespace App\Filament\Resources\VetVisits;

use App\Filament\Resources\VetVisits\Pages\CreateVetVisit;
use App\Filament\Resources\VetVisits\Pages\EditVetVisit;
use App\Filament\Resources\VetVisits\Pages\ListVetVisits;
use App\Filament\Resources\VetVisits\Pages\ViewVetVisit;
use App\Filament\Resources\VetVisits\Schemas\VetVisitForm;
use App\Filament\Resources\VetVisits\Schemas\VetVisitInfolist;
use App\Filament\Resources\VetVisits\Tables\VetVisitsTable;
use App\Models\VetVisit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VetVisitResource extends Resource
{
    protected static ?string $model = VetVisit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $recordTitleAttribute = 'visited_on';

    public static function form(Schema $schema): Schema
    {
        return VetVisitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VetVisitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VetVisitsTable::configure($table);
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
            'index' => ListVetVisits::route('/'),
            'create' => CreateVetVisit::route('/create'),
            'view' => ViewVetVisit::route('/{record}'),
            'edit' => EditVetVisit::route('/{record}/edit'),
        ];
    }
}
