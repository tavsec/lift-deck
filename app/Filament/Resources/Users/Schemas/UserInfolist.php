<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->copyable(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('bio')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('gym_name')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ColorEntry::make('primary_color')
                    ->placeholder('-'),
                ColorEntry::make('secondary_color')
                    ->placeholder('-'),
                ImageEntry::make('logo')
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('welcome_email_text')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('onboarding_welcome_text')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
