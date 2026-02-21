<?php

namespace App\Filament\Widgets;

use App\Models\DailyLog;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DailyLogsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Logging';
    protected function getStats(): array
    {
        $users = DailyLog::query()->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        $dates = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[$date] = $users[$date] ?? 0;
        }
        return [
            Stat::make("Days logged", DailyLog::query()->where("role", "client")->count())
                ->chart($dates->values()),
        ];
    }
}
