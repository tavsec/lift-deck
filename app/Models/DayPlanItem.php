<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayPlanItem extends Model
{
    /** @use HasFactory<\Database\Factories\DayPlanItemFactory> */
    use HasFactory;

    protected $fillable = [
        'day_plan_id',
        'meal_id',
        'off_code',
        'meal_type',
        'name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'portion_grams',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'calories' => 'integer',
            'protein' => 'decimal:1',
            'carbs' => 'decimal:1',
            'fat' => 'decimal:1',
            'portion_grams' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function dayPlan(): BelongsTo
    {
        return $this->belongsTo(DayPlan::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
