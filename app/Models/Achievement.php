<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Achievement extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\AchievementFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'type',
        'condition_type',
        'condition_value',
        'xp_reward',
        'points_reward',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the coach who created this achievement (null = global).
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get users who have earned this achievement.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['awarded_by', 'earned_at'])
            ->withTimestamps();
    }

    /**
     * Check if this is an automatic achievement.
     */
    public function isAutomatic(): bool
    {
        return $this->type === 'automatic';
    }

    /**
     * Check if this is a global (system) achievement.
     */
    public function isGlobal(): bool
    {
        return $this->coach_id === null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
            ->useDisk(config('filesystems.default'));
    }
}
