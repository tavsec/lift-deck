<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XpEventType extends Model
{
    /** @use HasFactory<\Database\Factories\XpEventTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'xp_amount',
        'points_amount',
        'is_active',
        'cooldown_hours',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
