<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingResponse extends Model
{
    protected $fillable = [
        'client_id',
        'onboarding_field_id',
        'value',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function onboardingField(): BelongsTo
    {
        return $this->belongsTo(OnboardingField::class);
    }
}
