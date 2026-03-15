<?php

namespace App\Filament\Resources\XpEventTypes\Pages;

use App\Filament\Resources\XpEventTypes\XpEventTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditXpEventType extends EditRecord
{
    protected static string $resource = XpEventTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
