<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('coach.name')
                    ->label('Coach')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('assignedTrackingMetrics_count')
                    ->label('Metrics')
                    ->counts('assignedTrackingMetrics')
                    ->badge(),
                IconColumn::make('is_track_only')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
