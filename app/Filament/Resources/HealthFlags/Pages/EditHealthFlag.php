<?php

namespace App\Filament\Resources\HealthFlags\Pages;

use App\Filament\Resources\HealthFlags\HealthFlagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHealthFlag extends EditRecord
{
    protected static string $resource = HealthFlagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
