<?php

namespace Database\Seeders;

use App\Models\XpEventType;
use Illuminate\Database\Seeder;

class XpEventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $eventTypes = [
            ['key' => 'workout_logged', 'name' => 'Workout Logged', 'description' => 'Awarded when a workout session is completed.', 'xp_amount' => 20, 'points_amount' => 20, 'cooldown_hours' => null],
            ['key' => 'daily_checkin', 'name' => 'Daily Check-in', 'description' => 'Awarded for logging daily metrics.', 'xp_amount' => 10, 'points_amount' => 10, 'cooldown_hours' => 24],
            ['key' => 'meal_logged', 'name' => 'Meal Logged', 'description' => 'Awarded for logging a meal.', 'xp_amount' => 5, 'points_amount' => 5, 'cooldown_hours' => null],
            ['key' => 'program_completed', 'name' => 'Program Completed', 'description' => 'Awarded when a training program is completed.', 'xp_amount' => 100, 'points_amount' => 100, 'cooldown_hours' => null],
            ['key' => 'streak_7_day', 'name' => '7-Day Streak', 'description' => 'Awarded for 7 consecutive days of daily check-ins.', 'xp_amount' => 50, 'points_amount' => 50, 'cooldown_hours' => 168],
            ['key' => 'streak_30_day', 'name' => '30-Day Streak', 'description' => 'Awarded for 30 consecutive days of daily check-ins.', 'xp_amount' => 200, 'points_amount' => 200, 'cooldown_hours' => 720],
        ];

        foreach ($eventTypes as $eventType) {
            XpEventType::updateOrCreate(
                ['key' => $eventType['key']],
                $eventType,
            );
        }
    }
}
