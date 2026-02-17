<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

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
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('role')
                    ->required(),
                Select::make('coach_id')
                    ->relationship('coach', 'name'),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                TextInput::make('avatar'),
                TextInput::make('gym_name'),
                TextInput::make('logo'),
                TextInput::make('primary_color'),
                TextInput::make('secondary_color'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('welcome_email_text')
                    ->columnSpanFull(),
                Textarea::make('onboarding_welcome_text')
                    ->columnSpanFull(),
            ]);
    }
}
