<?php

namespace App\Filament\Resources\Feedings\Pages;

use App\Filament\Resources\Feedings\FeedingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFeeding extends EditRecord
{
    protected static string $resource = FeedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
