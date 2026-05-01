<?php

namespace Database\Seeders;

use App\Models\ClientProgram;
use App\Models\Message;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds a polished demo coach + clients used to capture landing-page screenshots.
 *
 * Idempotent: re-running upserts the coach by email and rebuilds child records.
 */
class LandingDemoSeeder extends Seeder
{
    private const COACH_EMAIL = 'demo.coach@liftdeck.io';

    public function run(): void
    {
        /** @var User $coach */
        $coach = User::updateOrCreate(
            ['email' => self::COACH_EMAIL],
            [
                'name' => 'Sarah Mitchell',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'coach',
                'gym_name' => 'Iron Forge Coaching',
                'is_free_access' => true,
                'selected_plan' => 'professional',
                'trial_ends_at' => null,
                'metrics_onboarded_at' => now()->subDays(60),
                'onboarding_checklist_dismissed_at' => now()->subDays(60),
                'primary_color' => '#1456f0',
                'locale' => 'en',
            ]
        );

        $this->resetDemoData($coach);

        $clientNames = [
            'Emma Thompson',
            'Marcus Rivera',
            'Lily Anderson',
            'David Park',
            'Sophie Laurent',
            'James Walker',
            'Aisha Khan',
            'Noah Bennett',
        ];

        $clients = collect($clientNames)->map(function (string $name) use ($coach): User {
            $first = strtolower(explode(' ', $name)[0]);

            return User::updateOrCreate(
                ['email' => "demo.{$first}@liftdeck.io"],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role' => 'client',
                    'coach_id' => $coach->id,
                    'locale' => 'en',
                ]
            );
        });

        $programDefs = [
            ['name' => '12-Week Strength Build', 'type' => 'strength', 'duration_weeks' => 12],
            ['name' => 'Hypertrophy Phase 2', 'type' => 'hypertrophy', 'duration_weeks' => 8],
            ['name' => 'Fat Loss Foundations', 'type' => 'fat_loss', 'duration_weeks' => 10],
        ];

        $programs = collect($programDefs)->map(function (array $p) use ($coach): Program {
            return Program::create([
                'coach_id' => $coach->id,
                'name' => $p['name'],
                'description' => 'Coach-built program for committed clients.',
                'duration_weeks' => $p['duration_weeks'],
                'type' => $p['type'],
                'is_template' => false,
            ]);
        });

        $workoutDefs = [
            ['name' => 'Upper Body — Push', 'order' => 1],
            ['name' => 'Lower Body — Squat Focus', 'order' => 2],
            ['name' => 'Upper Body — Pull', 'order' => 3],
            ['name' => 'Conditioning & Core', 'order' => 4],
        ];

        $programWorkouts = $programs->flatMap(function (Program $program) use ($workoutDefs) {
            return collect($workoutDefs)->map(function (array $w) use ($program): ProgramWorkout {
                return ProgramWorkout::create([
                    'program_id' => $program->id,
                    'name' => $w['name'],
                    'day_number' => $w['order'],
                    'order' => $w['order'],
                ]);
            });
        });

        $clients->each(function (User $client) use ($programs, $programWorkouts): void {
            $program = $programs->random();

            $clientProgram = ClientProgram::create([
                'client_id' => $client->id,
                'program_id' => $program->id,
                'started_at' => now()->subDays(random_int(7, 60)),
                'status' => 'active',
            ]);

            $programWorkoutsForProgram = $programWorkouts->where('program_id', $program->id)->values();

            $logCount = random_int(3, 8);
            for ($i = 0; $i < $logCount; $i++) {
                WorkoutLog::create([
                    'client_id' => $client->id,
                    'client_program_id' => $clientProgram->id,
                    'program_workout_id' => $programWorkoutsForProgram->random()->id,
                    'completed_at' => Carbon::now()->subHours(random_int(1, 24 * 14)),
                    'notes' => null,
                ]);
            }
        });

        $messageSnippets = [
            'Just wrapped today\'s session — felt strong on the squats!',
            'Quick question about the deload week. Still hit upper body?',
            'Hit a new PR on bench: 95kg for 3 reps 💪',
            'Feeling tight in my lower back — any mobility recs?',
        ];

        $clients->take(4)->each(function (User $client, int $i) use ($coach, $messageSnippets): void {
            Message::create([
                'sender_id' => $client->id,
                'receiver_id' => $coach->id,
                'body' => $messageSnippets[$i] ?? $messageSnippets[0],
                'read_at' => null,
                'created_at' => now()->subHours($i + 1),
                'updated_at' => now()->subHours($i + 1),
            ]);
        });
    }

    private function resetDemoData(User $coach): void
    {
        $clientIds = $coach->clients()->pluck('id');

        WorkoutLog::whereIn('client_id', $clientIds)->delete();
        ClientProgram::whereIn('client_id', $clientIds)->delete();

        Message::where(function ($query) use ($coach, $clientIds): void {
            $query->where('receiver_id', $coach->id)->whereIn('sender_id', $clientIds);
        })->orWhere(function ($query) use ($coach, $clientIds): void {
            $query->where('sender_id', $coach->id)->whereIn('receiver_id', $clientIds);
        })->delete();

        ProgramWorkout::whereIn('program_id', $coach->programs()->pluck('id'))->delete();
        Program::where('coach_id', $coach->id)->delete();

        User::where('coach_id', $coach->id)
            ->where('email', 'like', 'demo.%@liftdeck.io')
            ->forceDelete();
    }
}
