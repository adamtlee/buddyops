<?php

namespace App\Filament\Resources\VetVisits\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VetVisitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('dog.name')
                    ->label('Dog'),
                TextEntry::make('visited_on')
                    ->date(),
                TextEntry::make('clinic')
                    ->placeholder('-'),
                TextEntry::make('reason'),
                TextEntry::make('diagnosis')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('next_due_on')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
