<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('dog_id')
                    ->relationship('dog', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                Select::make('type')
                    ->options(['vet_record' => 'Vet record', 'food_label' => 'Food label', 'training' => 'Training', 'other' => 'Other'])
                    ->default('other')
                    ->required(),
                FileUpload::make('file_path')
                    ->directory('dog-documents')
                    ->acceptedFileTypes(['application/pdf'])
                    ->required(),
                Select::make('status')
                    ->options(['uploaded' => 'Uploaded', 'processed' => 'Processed', 'failed' => 'Failed'])
                    ->default('uploaded')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
