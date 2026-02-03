<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutLogComment extends Model
{
    /** @use HasFactory<\Database\Factories\WorkoutLogCommentFactory> */
    use HasFactory;

    protected $fillable = [
        'workout_log_id',
        'user_id',
        'body',
    ];

    public function workoutLog(): BelongsTo
    {
        return $this->belongsTo(WorkoutLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
