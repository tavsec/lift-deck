<?php

namespace App\Filament\Resources\Achievements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description'),
                Select::make('type')
                    ->options([
                        'automatic' => 'Automatic',
                        'manual' => 'Manual',
                    ])
                    ->required()
                    ->live(),
                Select::make('condition_type')
                    ->options([
                        'workout_count' => 'Workout Count',
                        'checkin_count' => 'Check-in Count',
                        'xp_total' => 'Total XP',
                        'streak_days' => 'Streak Days',
                    ])
                    ->visible(fn (Get $get): bool => $get('type') === 'automatic'),
                TextInput::make('condition_value')
                    ->numeric()
                    ->visible(fn (Get $get): bool => $get('type') === 'automatic'),
                TextInput::make('xp_reward')
                    ->numeric()
                    ->default(0),
                TextInput::make('points_reward')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
                SpatieMediaLibraryFileUpload::make('icon')
                    ->collection('icon'),
            ]);
    }
}
