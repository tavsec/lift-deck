<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'meal_id',
        'date',
        'meal_type',
        'name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'protein' => 'decimal:1',
            'carbs' => 'decimal:1',
            'fat' => 'decimal:1',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
