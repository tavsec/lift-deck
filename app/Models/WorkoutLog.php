<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutLog extends Model
{
    /** @use HasFactory<\Database\Factories\WorkoutLogFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_program_id',
        'program_workout_id',
        'custom_name',
        'completed_at',
        'notes',
    ];

    /**
     * Get the display name for this workout log.
     */
    public function displayName(): string
    {
        return $this->custom_name ?? $this->programWorkout?->name ?? 'Workout';
    }

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function clientProgram(): BelongsTo
    {
        return $this->belongsTo(ClientProgram::class);
    }

    public function programWorkout(): BelongsTo
    {
        return $this->belongsTo(ProgramWorkout::class);
    }

    public function exerciseLogs(): HasMany
    {
        return $this->hasMany(ExerciseLog::class)->orderBy('workout_exercise_id')->orderBy('set_number');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(WorkoutLogComment::class)->orderBy('created_at');
    }
}
