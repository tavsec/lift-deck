<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrackingMetric extends Model
{
    /** @use HasFactory<\Database\Factories\TrackingMetricFactory> */
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'type',
        'unit',
        'scale_min',
        'scale_max',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'scale_min' => 'integer',
            'scale_max' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function clientAssignments(): HasMany
    {
        return $this->hasMany(ClientTrackingMetric::class);
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    /**
     * Seed default metrics for a coach, using their locale for names.
     *
     * @return array<int, self>
     */
    public static function seedDefaults(int $coachId, string $locale = 'en'): array
    {
        $defaults = [
            [
                'name' => __('coach.default_metrics.weight', locale: $locale),
                'type' => 'number',
                'unit' => 'kg',
                'order' => 1,
            ],
            [
                'name' => __('coach.default_metrics.steps', locale: $locale),
                'type' => 'number',
                'unit' => 'steps',
                'order' => 2,
            ],
            [
                'name' => __('coach.default_metrics.progress_image', locale: $locale),
                'type' => 'image',
                'order' => 3,
            ],
            [
                'name' => __('coach.default_metrics.mood', locale: $locale),
                'type' => 'scale',
                'order' => 4,
            ],
            [
                'name' => __('coach.default_metrics.energy', locale: $locale),
                'type' => 'scale',
                'order' => 5,
            ],
            [
                'name' => __('coach.default_metrics.sleep', locale: $locale),
                'type' => 'scale',
                'order' => 6,
            ],
        ];

        $metrics = [];
        foreach ($defaults as $default) {
            $metrics[] = self::create(array_merge($default, ['coach_id' => $coachId]));
        }

        return $metrics;
    }
}
