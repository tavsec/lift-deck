<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDayAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\ClientDayAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id',
        'coach_id',
        'day_plan_id',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function dayPlan(): BelongsTo
    {
        return $this->belongsTo(DayPlan::class);
    }
}
