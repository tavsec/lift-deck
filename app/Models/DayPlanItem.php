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
        'meal_type',
        'sort_order',
    ];

    public function dayPlan(): BelongsTo
    {
        return $this->belongsTo(DayPlan::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
