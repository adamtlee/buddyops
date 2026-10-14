<?php

namespace App\Filament\Resources\Feedings\Pages;

use App\Filament\Resources\Feedings\FeedingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeedings extends ListRecords
{
    protected static string $resource = FeedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
