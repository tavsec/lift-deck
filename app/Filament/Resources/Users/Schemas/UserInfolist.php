<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Features\Loyalty;
use App\Services\SubscriptionService;
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

                Section::make('Subscription')
                    ->schema([
                        TextEntry::make('subscription_plan')
                            ->label('Plan')
                            ->state(fn ($record): string => ucfirst(app(SubscriptionService::class)->currentPlanKey($record) ?? 'None'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Basic' => 'info',
                                'Advanced' => 'warning',
                                'Professional' => 'success',
                                default => 'gray',
                            }),
                        TextEntry::make('subscription_status')
                            ->label('Status')
                            ->state(function ($record): string {
                                $sub = $record->subscription('default');
                                if ($sub) {
                                    return $sub->stripe_status;
                                }

                                return $record->onTrial() ? 'trial' : 'none';
                            })
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'trialing', 'trial' => 'info',
                                'past_due', 'unpaid' => 'warning',
                                'canceled', 'none' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('trial_ends_at')
                            ->label('Trial Ends At')
                            ->state(fn ($record) => $record->trial_ends_at ?? $record->subscription('default')?->trial_ends_at)
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('subscription_ends_at')
                            ->label('Subscription Ends At')
                            ->state(fn ($record) => $record->subscription('default')?->ends_at)
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('stripe_id')
                            ->label('Stripe Customer ID')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('stripe_subscription_id')
                            ->label('Stripe Subscription ID')
                            ->state(fn ($record) => $record->subscription('default')?->stripe_id)
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('is_free_access')
                            ->label('Free Access')
                            ->state(fn ($record): string => $record->is_free_access ? 'Granted' : 'Not granted')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'Granted' ? 'success' : 'gray')
                            ->helperText('Full Professional access without a subscription (ambassadors, friends, etc.)')
                            ->suffixAction(
                                Action::make('toggle_free_access')
                                    ->label(fn (TextEntry $component): string => $component->getState() === 'Granted' ? 'Revoke' : 'Grant')
                                    ->color(fn (TextEntry $component): string => $component->getState() === 'Granted' ? 'danger' : 'success')
                                    ->button()
                                    ->action(fn ($record): mixed => $record->update(['is_free_access' => ! $record->is_free_access])),
                            ),
                    ]),

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
