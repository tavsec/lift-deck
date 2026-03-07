<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Features\Loyalty;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Laravel\Pennant\Feature;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->disabledOn('edit'),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->disabledOn('edit'),
                TextInput::make('role')
                    ->default('coach')
                    ->disabled()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                TextInput::make('gym_name'),
                ColorPicker::make('primary_color'),
                ColorPicker::make('secondary_color'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('welcome_email_text')
                    ->columnSpanFull(),
                Textarea::make('onboarding_welcome_text')
                    ->columnSpanFull(),
                Section::make('Features')
                    ->schema([
                        Toggle::make('feature_loyalty')
                            ->label('Loyalty System')
                            ->helperText('Enables XP, levels, achievements, and rewards for this coach and their clients.')
                            ->default(fn ($record) => $record ? Feature::for($record)->active(Loyalty::class) : false)
                            ->afterStateUpdated(function ($record, bool $state): void {
                                if (! $record) {
                                    return;
                                }
                                $state
                                    ? Feature::for($record)->activate(Loyalty::class)
                                    : Feature::for($record)->deactivate(Loyalty::class);
                            })
                            ->live()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
