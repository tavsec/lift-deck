<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_workout_id',
        'exercise_id',
        'sets',
        'reps',
        'rest_seconds',
        'notes',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'sets' => 'integer',
            'rest_seconds' => 'integer',
            'order' => 'integer',
        ];
    }

    public function programWorkout(): BelongsTo
    {
        return $this->belongsTo(ProgramWorkout::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    public function getFormattedRestAttribute(): ?string
    {
        if (! $this->rest_seconds) {
            return null;
        }

        if ($this->rest_seconds >= 60) {
            $minutes = floor($this->rest_seconds / 60);
            $seconds = $this->rest_seconds % 60;

            return $seconds > 0 ? "{$minutes}m {$seconds}s" : "{$minutes}m";
        }

        return "{$this->rest_seconds}s";
    }
}
