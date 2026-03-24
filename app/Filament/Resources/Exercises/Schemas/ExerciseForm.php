<?php

namespace App\Filament\Resources\Exercises\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExerciseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description'),
                Select::make('muscle_group')
                    ->required()
                    ->options([
                        'chest' => 'Chest',
                        'back' => 'Back',
                        'shoulders' => 'Shoulders',
                        'biceps' => 'Biceps',
                        'triceps' => 'Triceps',
                        'forearms' => 'Forearms',
                        'core' => 'Core',
                        'quadriceps' => 'Quadriceps',
                        'hamstrings' => 'Hamstrings',
                        'glutes' => 'Glutes',
                        'calves' => 'Calves',
                        'full_body' => 'Full Body',
                        'cardio' => 'Cardio',
                    ]),
                TextInput::make('video_url')
                    ->url()
                    ->maxLength(500),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
