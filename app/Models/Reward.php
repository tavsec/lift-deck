<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Reward extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\RewardFactory> */
    use HasFactory;

    use InteractsWithMedia;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'points_cost',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the coach who created this reward (null = global).
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get redemptions for this reward.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Check if this reward is a global (system) reward.
     */
    public function isGlobal(): bool
    {
        return $this->coach_id === null;
    }

    /**
     * Check if this reward has available stock.
     */
    public function hasStock(): bool
    {
        if ($this->stock === null) {
            return true;
        }

        return $this->stock > 0;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk(config('filesystems.default'));
    }
}
