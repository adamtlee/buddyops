<?php

namespace App\Filament\Resources\VetVisits\Pages;

use App\Filament\Resources\VetVisits\VetVisitResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVetVisit extends ViewRecord
{
    protected static string $resource = VetVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
