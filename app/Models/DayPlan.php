<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DayPlan extends Model
{
    /** @use HasFactory<\Database\Factories\DayPlanFactory> */
    use HasFactory;

    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DayPlanItem::class)->orderBy('sort_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ClientDayAssignment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function totalCalories(): Attribute
    {
        return Attribute::get(function (): int {
            $this->loadMissing('items.meal');

            return (int) $this->items->sum(fn (DayPlanItem $item): int => (int) ($item->meal->calories ?? 0));
        });
    }

    protected function totalProtein(): Attribute
    {
        return Attribute::get(function (): float {
            $this->loadMissing('items.meal');

            return (float) $this->items->sum(fn (DayPlanItem $item): float => (float) ($item->meal->protein ?? 0));
        });
    }

    protected function totalCarbs(): Attribute
    {
        return Attribute::get(function (): float {
            $this->loadMissing('items.meal');

            return (float) $this->items->sum(fn (DayPlanItem $item): float => (float) ($item->meal->carbs ?? 0));
        });
    }

    protected function totalFat(): Attribute
    {
        return Attribute::get(function (): float {
            $this->loadMissing('items.meal');

            return (float) $this->items->sum(fn (DayPlanItem $item): float => (float) ($item->meal->fat ?? 0));
        });
    }
}
