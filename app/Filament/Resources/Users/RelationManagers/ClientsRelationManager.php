<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\User;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->url(fn (User $record): string => ClientResource::getUrl('view', ['record' => $record])),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('assignedTrackingMetrics.trackingMetric.name')
                    ->label('Tracking Metrics')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->placeholder('None'),
            ])
            ->filters([]);
    }
}
