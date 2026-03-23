<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Features\Loyalty;
use Filament\Actions\Action;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Laravel\Pennant\Feature;

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
                TextEntry::make('stripe_id')
                    ->label('Stripe Customer ID')
                    ->placeholder('Not yet created'),
                TextEntry::make('trial_ends_at')
                    ->label('Trial Ends At')
                    ->dateTime()
                    ->placeholder('No trial'),
                TextEntry::make('is_free_access')
                    ->label('Free Access')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                Section::make('Features')
                    ->schema([
                        TextEntry::make('loyalty_feature_status')
                            ->label('Loyalty System')
                            ->helperText('Enables XP, levels, achievements, and rewards for this coach and their clients.')
                            ->state(fn ($record) => Feature::for($record)->active(Loyalty::class) ? 'Enabled' : 'Disabled')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'Enabled' ? 'success' : 'danger')
                            ->suffixAction(
                                Action::make('toggle_loyalty')
                                    ->label(fn (TextEntry $component): string => $component->getState() === 'Enabled' ? 'Disable' : 'Enable')
                                    ->color(fn (TextEntry $component): string => $component->getState() === 'Enabled' ? 'danger' : 'success')
                                    ->button()
                                    ->action(function ($record): void {
                                        Feature::for($record)->active(Loyalty::class)
                                            ? Feature::for($record)->deactivate(Loyalty::class)
                                            : Feature::for($record)->activate(Loyalty::class);
                                    }),
                            ),
                    ]),
            ]);
    }
}
