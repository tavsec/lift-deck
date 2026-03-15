<?php

namespace App\Filament\Resources\Rewards\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RewardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description'),
                TextInput::make('points_cost')
                    ->numeric()
                    ->required(),
                TextInput::make('stock')
                    ->numeric()
                    ->nullable(),
                Toggle::make('is_active')
                    ->default(true),
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('image'),
            ]);
    }
}
