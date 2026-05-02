<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealLogComment extends Model
{
    /** @use HasFactory<\Database\Factories\MealLogCommentFactory> */
    use HasFactory;

    protected $fillable = [
        'meal_log_id',
        'author_id',
        'body',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function mealLog(): BelongsTo
    {
        return $this->belongsTo(MealLog::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
