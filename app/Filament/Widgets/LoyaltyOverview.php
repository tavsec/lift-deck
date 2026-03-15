<?php

namespace App\Filament\Widgets;

use App\Models\RewardRedemption;
use App\Models\UserXpSummary;
use App\Models\XpTransaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoyaltyOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Loyalty';

    protected function getStats(): array
    {
        return [
            Stat::make('Total XP Awarded', XpTransaction::sum('xp_amount')),
            Stat::make('Total Redemptions', RewardRedemption::count()),
            Stat::make('Active Users', UserXpSummary::where('total_xp', '>', 0)->count()),
        ];
    }
}
