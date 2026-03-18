<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MealLog extends Model
{
    use HasFactory, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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
