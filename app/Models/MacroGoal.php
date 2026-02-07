<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MacroGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'coach_id',
        'calories',
        'protein',
        'carbs',
        'fat',
        'effective_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'protein' => 'decimal:1',
            'carbs' => 'decimal:1',
            'fat' => 'decimal:1',
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

    /**
     * Get the active macro goal for a client on a given date.
     */
    public static function activeForClient(int $clientId, string $date): ?self
    {
        return static::where('client_id', $clientId)
            ->where('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->first();
    }
}
