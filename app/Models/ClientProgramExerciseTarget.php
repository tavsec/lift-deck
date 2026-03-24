<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProgramExerciseTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_program_id',
        'workout_exercise_id',
        'target_weight',
    ];

    protected function casts(): array
    {
        return [
            'target_weight' => 'decimal:2',
        ];
    }

    public function clientProgram(): BelongsTo
    {
        return $this->belongsTo(ClientProgram::class);
    }

    public function workoutExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutExercise::class);
    }
}
