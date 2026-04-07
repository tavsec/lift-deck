<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Services\SubscriptionService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('subscriptions'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn (User $record): ?string => $record->description)
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->copyable()
                    ->icon(Heroicon::Envelope)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subscription_plan')
                    ->label('Plan')
                    ->state(fn (User $record): string => ucfirst(app(SubscriptionService::class)->currentPlanKey($record) ?? 'None'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Basic' => 'info',
                        'Advanced' => 'warning',
                        'Professional' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('subscription_status')
                    ->label('Status')
                    ->state(function (User $record): string {
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
                IconColumn::make('is_free_access')
                    ->label('Free Access')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('clients_count')
                    ->label('Number of clients')
                    ->sortable()
                    ->counts('clients')
                    ->badge(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('gym_name')
                    ->searchable(),
                ImageColumn::make('logo'),
                ColorColumn::make('primary_color'),
                ColorColumn::make('secondary_color'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
