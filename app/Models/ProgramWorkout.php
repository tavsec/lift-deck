<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramWorkout extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'name',
        'day_number',
        'notes',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
            'order' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(WorkoutExercise::class)->orderBy('order');
    }

    public function getTotalSetsAttribute(): int
    {
        return $this->exercises->sum('sets');
    }
}
