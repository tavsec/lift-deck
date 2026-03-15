<?php

namespace App\Filament\Resources\XpEventTypes\Pages;

use App\Filament\Resources\XpEventTypes\XpEventTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListXpEventTypes extends ListRecords
{
    protected static string $resource = XpEventTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
