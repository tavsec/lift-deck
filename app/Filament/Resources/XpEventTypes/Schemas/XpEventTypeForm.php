<?php

namespace App\Filament\Resources\XpEventTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class XpEventTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->disabledOn('edit'),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description'),
                TextInput::make('xp_amount')
                    ->numeric()
                    ->required(),
                TextInput::make('points_amount')
                    ->numeric()
                    ->required(),
                TextInput::make('cooldown_hours')
                    ->numeric()
                    ->nullable(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
