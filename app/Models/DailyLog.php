<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DailyLog extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DailyLogFactory> */
    use HasFactory;

    use InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('check-in-image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk(config('filesystems.default'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->nonQueued();

        $this->addMediaConversion('full')
            ->width(1920)
            ->height(1920)
            ->nonQueued();
    }
}
