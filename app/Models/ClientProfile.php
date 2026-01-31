<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProfile extends Model
{
    protected $fillable = [
        'user_id',
        'goal',
        'experience_level',
        'injuries',
        'equipment_access',
        'availability',
        'onboarding_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'availability' => 'array',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOnboardingComplete(): bool
    {
        return $this->onboarding_completed_at !== null;
    }
}
