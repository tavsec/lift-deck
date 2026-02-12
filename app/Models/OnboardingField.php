<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingField extends Model
{
    /** @use HasFactory<\Database\Factories\OnboardingFieldFactory> */
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'label',
        'type',
        'options',
        'is_required',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(OnboardingResponse::class);
    }
}
