<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserXpSummary extends Model
{
    /** @use HasFactory<\Database\Factories\UserXpSummaryFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_xp',
        'available_points',
        'current_level_id',
    ];

    /**
     * Get the user that owns this XP summary.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the current level for this user.
     */
    public function currentLevel(): BelongsTo
    {
        return $this->belongsTo(Level::class, 'current_level_id');
    }
}
