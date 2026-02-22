<?php

namespace App\Filament\Resources\XpEventTypes;

use App\Filament\Resources\XpEventTypes\Pages\CreateXpEventType;
use App\Filament\Resources\XpEventTypes\Pages\EditXpEventType;
use App\Filament\Resources\XpEventTypes\Pages\ListXpEventTypes;
use App\Filament\Resources\XpEventTypes\Schemas\XpEventTypeForm;
use App\Filament\Resources\XpEventTypes\Tables\XpEventTypesTable;
use App\Models\XpEventType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class XpEventTypeResource extends Resource
{
    protected static ?string $model = XpEventType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|UnitEnum|null $navigationGroup = 'Loyalty';

    public static function form(Schema $schema): Schema
    {
        return XpEventTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return XpEventTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListXpEventTypes::route('/'),
            'create' => CreateXpEventType::route('/create'),
            'edit' => EditXpEventType::route('/{record}/edit'),
        ];
    }
}
