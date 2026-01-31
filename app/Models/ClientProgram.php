<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProgram extends Model
{
    protected $fillable = [
        'client_id',
        'program_id',
        'started_at',
        'completed_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'completed_at' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
