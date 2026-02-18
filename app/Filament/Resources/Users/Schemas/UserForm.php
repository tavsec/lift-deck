<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
                    ->required()
                    ->disabled(),
                TextInput::make('role')
                    ->default("coach")
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
            ]);
    }
}
