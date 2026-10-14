<?php

namespace App\Filament\Resources\Walks\Pages;

use App\Filament\Resources\Walks\WalkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWalks extends ListRecords
{
    protected static string $resource = WalkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
