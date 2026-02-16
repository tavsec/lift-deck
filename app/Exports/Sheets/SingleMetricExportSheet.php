<?php

namespace App\Exports\Sheets;

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SingleMetricExportSheet implements FromCollection, WithHeadings, WithTitle
{
    protected ClientTrackingMetric $metric;
    public function __construct(ClientTrackingMetric $metric){
        $this->metric = $metric;
    }

    public function collection()
    {
        $metric = $this->metric;
        $dailyLogs = DailyLog::where('tracking_metric_id', $metric->tracking_metric_id)
            ->where("client_id", $metric->client_id)
            ->get()->map(fn($item) => [$item->date, $item->value]);

        return $dailyLogs;
        // TODO: Implement collection() method.
    }

    public function headings(): array
    {
        return ["date", "value"];
    }

    public function title(): string
    {
        return $this->metric->trackingMetric->name;
    }
}
