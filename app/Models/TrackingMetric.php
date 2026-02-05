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
     * Preseed default metrics for a coach.
     *
     * @return array<int, self>
     */
    public static function seedDefaults(int $coachId): array
    {
        $defaults = [
            ['name' => 'Body Weight', 'type' => 'number', 'unit' => 'kg', 'order' => 1],
            ['name' => 'Steps', 'type' => 'number', 'unit' => 'steps', 'order' => 2],
            ['name' => 'Sleep Quality', 'type' => 'scale', 'order' => 3],
            ['name' => 'Energy Level', 'type' => 'scale', 'order' => 4],
            ['name' => 'Mood', 'type' => 'scale', 'order' => 5],
            ['name' => 'Took Supplements', 'type' => 'boolean', 'order' => 6],
            ['name' => 'Daily Notes', 'type' => 'text', 'order' => 7],
        ];

        $metrics = [];
        foreach ($defaults as $default) {
            $metrics[] = self::create(array_merge($default, ['coach_id' => $coachId]));
        }

        return $metrics;
    }
}
