<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'duration_weeks',
        'type',
        'is_template',
    ];

    protected function casts(): array
    {
        return [
            'is_template' => 'boolean',
            'duration_weeks' => 'integer',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(ProgramWorkout::class)->orderBy('order');
    }

    public function clientPrograms(): HasMany
    {
        return $this->hasMany(ClientProgram::class);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeForCoach($query, $coachId)
    {
        return $query->where('coach_id', $coachId);
    }

    public function getTotalExercisesAttribute(): int
    {
        return $this->workouts->sum(fn ($workout) => $workout->exercises->count());
    }

    public function getTypeOptions(): array
    {
        return [
            'strength' => 'Strength',
            'hypertrophy' => 'Hypertrophy',
            'fat_loss' => 'Fat Loss',
            'general' => 'General Fitness',
        ];
    }
}
