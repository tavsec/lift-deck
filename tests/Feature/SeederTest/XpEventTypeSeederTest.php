<?php

use App\Models\XpEventType;
use Database\Seeders\XpEventTypeSeeder;

it('seeds all xp event types', function () {
    $this->seed(XpEventTypeSeeder::class);

    expect(XpEventType::count())->toBe(6);
});

it('seeds expected event type keys', function () {
    $this->seed(XpEventTypeSeeder::class);

    $expectedKeys = [
        'workout_logged',
        'daily_checkin',
        'meal_logged',
        'program_completed',
        'streak_7_day',
        'streak_30_day',
    ];

    foreach ($expectedKeys as $key) {
        expect(XpEventType::where('key', $key)->exists())->toBeTrue();
    }
});

it('is idempotent when run multiple times', function () {
    $this->seed(XpEventTypeSeeder::class);
    $this->seed(XpEventTypeSeeder::class);

    expect(XpEventType::count())->toBe(6);
});

it('seeds correct xp amounts', function () {
    $this->seed(XpEventTypeSeeder::class);

    expect(XpEventType::where('key', 'workout_logged')->first()->xp_amount)->toBe(20)
        ->and(XpEventType::where('key', 'program_completed')->first()->xp_amount)->toBe(100)
        ->and(XpEventType::where('key', 'streak_30_day')->first()->xp_amount)->toBe(200);
});

it('seeds cooldown hours correctly', function () {
    $this->seed(XpEventTypeSeeder::class);

    expect(XpEventType::where('key', 'daily_checkin')->first()->cooldown_hours)->toBe(24)
        ->and(XpEventType::where('key', 'workout_logged')->first()->cooldown_hours)->toBeNull();
});
