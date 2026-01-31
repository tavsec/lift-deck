<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    protected $fillable = [
        'name',
        'description',
        'muscle_group',
        'video_url',
        'coach_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function isGlobal(): bool
    {
        return $this->coach_id === null;
    }

    public function isCustom(): bool
    {
        return $this->coach_id !== null;
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('coach_id');
    }

    public function scopeForCoach($query, $coachId)
    {
        return $query->where(function ($q) use ($coachId) {
            $q->whereNull('coach_id')
                ->orWhere('coach_id', $coachId);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getYoutubeEmbedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        // Extract YouTube video ID from various URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtube\.com\/embed\/([^?]+)/',
            '/youtu\.be\/([^?]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $this->video_url, $matches)) {
                return 'https://www.youtube.com/embed/'.$matches[1];
            }
        }

        return null;
    }
}
