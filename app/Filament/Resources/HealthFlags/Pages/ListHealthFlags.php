<?php

namespace App\Filament\Resources\HealthFlags\Pages;

use App\Filament\Resources\HealthFlags\HealthFlagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHealthFlags extends ListRecords
{
    protected static string $resource = HealthFlagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
