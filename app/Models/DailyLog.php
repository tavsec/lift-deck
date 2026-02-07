<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyLog extends Model
{
    /** @use HasFactory<\Database\Factories\DailyLogFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'tracking_metric_id',
        'date',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function trackingMetric(): BelongsTo
    {
        return $this->belongsTo(TrackingMetric::class);
    }
}
