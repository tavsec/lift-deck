<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTrackingMetric extends Model
{
    /** @use HasFactory<\Database\Factories\ClientTrackingMetricFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'tracking_metric_id',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
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
