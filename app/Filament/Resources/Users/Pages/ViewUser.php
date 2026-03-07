<?php

namespace App\Filament\Resources\Users\Pages;

use App\Features\Loyalty;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Laravel\Pennant\Feature;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('toggle_loyalty')
                ->label(fn () => Feature::for($this->getRecord())->active(Loyalty::class) ? 'Disable Loyalty' : 'Enable Loyalty')
                ->color(fn () => Feature::for($this->getRecord())->active(Loyalty::class) ? 'danger' : 'success')
                ->action(function (): void {
                    $record = $this->getRecord();
                    Feature::for($record)->active(Loyalty::class)
                        ? Feature::for($record)->deactivate(Loyalty::class)
                        : Feature::for($record)->activate(Loyalty::class);
                }),
        ];
    }
}
