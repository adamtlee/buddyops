<?php

namespace App\Filament\Resources\Walks\Pages;

use App\Filament\Resources\Walks\WalkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWalk extends EditRecord
{
    protected static string $resource = WalkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
