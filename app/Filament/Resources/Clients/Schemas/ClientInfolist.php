<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Resources\Users\UserResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->copyable(),
                TextEntry::make('coach.name')
                    ->label('Coach')
                    ->placeholder('No coach assigned')
                    ->url(fn ($record): ?string => $record->coach
                        ? UserResource::getUrl('view', ['record' => $record->coach])
                        : null),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('is_track_only')
                    ->label('Track-only client')
                    ->badge()
                    ->state(fn ($record) => $record->is_track_only ? 'Yes' : 'No')
                    ->color(fn (string $state): string => $state === 'Yes' ? 'warning' : 'success'),
                Section::make('Tracking Metrics')
                    ->schema([
                        RepeatableEntry::make('assignedTrackingMetrics')
                            ->label('')
                            ->schema([
                                TextEntry::make('trackingMetric.name')
                                    ->label('Metric'),
                                TextEntry::make('trackingMetric.type')
                                    ->label('Type')
                                    ->badge(),
                                TextEntry::make('trackingMetric.unit')
                                    ->label('Unit')
                                    ->placeholder('-'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
