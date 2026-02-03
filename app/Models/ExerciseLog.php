<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseLog extends Model
{
    /** @use HasFactory<\Database\Factories\ExerciseLogFactory> */
    use HasFactory;

    protected $fillable = [
        'workout_log_id',
        'workout_exercise_id',
        'exercise_id',
        'set_number',
        'weight',
        'reps',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'reps' => 'integer',
            'set_number' => 'integer',
        ];
    }

    public function workoutLog(): BelongsTo
    {
        return $this->belongsTo(WorkoutLog::class);
    }

    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
