<?php

namespace App\Exports;

use App\Exports\Sheets\SingleMetricExportSheet;
use App\Models\ClientTrackingMetric;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CoachAnalyticsExport implements WithMultipleSheets
{
    use Exportable;
    protected User $client;

    public function __construct(User $client){
        $this->client = $client;
    }

    public function sheets(): array{
        $sheets = [];
        $allClientMetrics = ClientTrackingMetric::where("client_id",$this->client->id)->get();

        foreach($allClientMetrics as $metric){
            if($metric->trackingMetric->type === "image") continue;
            $sheets[] = new SingleMetricExportSheet($metric);
        }

        return $sheets;
    }
}
