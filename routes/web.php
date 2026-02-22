<?php

use App\Http\Controllers\Client;
use App\Http\Controllers\Coach;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Client registration via invitation code
Route::middleware('guest')->group(function () {
    Route::get('join', [App\Http\Controllers\Auth\ClientRegistrationController::class, 'showCodeForm'])->name('join');
    Route::get('join/{code}', [App\Http\Controllers\Auth\ClientRegistrationController::class, 'showRegistrationForm'])->name('join.code');
    Route::post('join', [App\Http\Controllers\Auth\ClientRegistrationController::class, 'register'])->name('join.register');
});

// Coach routes
Route::middleware(['auth', 'verified', 'role:coach'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('/', Coach\DashboardController::class)->name('dashboard');
        Route::resource('clients', Coach\ClientController::class);
        Route::get('clients/{client}/workout-log/{workoutLog}', [Coach\ClientController::class, 'workoutLog'])->name('clients.workout-log');
        Route::post('clients/{client}/workout-log/{workoutLog}/comment', [Coach\ClientController::class, 'workoutLogComment'])->name('clients.workout-log.comment');
        Route::post('clients/{client}/toggle-metric', [Coach\ClientController::class, 'toggleMetric'])->name('clients.toggle-metric');
        Route::resource('programs', Coach\ProgramController::class);

        // Program workout routes
        Route::post('programs/{program}/workouts', [Coach\ProgramController::class, 'addWorkout'])->name('programs.workouts.store');
        Route::put('programs/{program}/workouts/{workout}', [Coach\ProgramController::class, 'updateWorkout'])->name('programs.workouts.update');
        Route::delete('programs/{program}/workouts/{workout}', [Coach\ProgramController::class, 'deleteWorkout'])->name('programs.workouts.destroy');

        // Program exercise routes
        Route::post('programs/{program}/workouts/{workout}/exercises', [Coach\ProgramController::class, 'addExercise'])->name('programs.exercises.store');
        Route::put('programs/{program}/exercises/{workoutExercise}', [Coach\ProgramController::class, 'updateExercise'])->name('programs.exercises.update');
        Route::delete('programs/{program}/exercises/{workoutExercise}', [Coach\ProgramController::class, 'deleteExercise'])->name('programs.exercises.destroy');
        Route::post('programs/{program}/exercises/{workoutExercise}/move-up', [Coach\ProgramController::class, 'moveExerciseUp'])->name('programs.exercises.move-up');
        Route::post('programs/{program}/exercises/{workoutExercise}/move-down', [Coach\ProgramController::class, 'moveExerciseDown'])->name('programs.exercises.move-down');

        // Program assignment routes
        Route::get('programs/{program}/assign', [Coach\ProgramController::class, 'assignForm'])->name('programs.assign');
        Route::post('programs/{program}/assign', [Coach\ProgramController::class, 'assign'])->name('programs.assign.store');

        Route::get('clients/{client}/nutrition', [Coach\NutritionController::class, 'show'])->name('clients.nutrition');
        Route::get('clients/{client}/analytics', [Coach\AnalyticsController::class, 'show'])->name('clients.analytics');
        Route::get('clients/{client}/analytics/export', [Coach\AnalyticsController::class, 'exportToExcel'])->name('clients.analytics.export');
        Route::post('clients/{client}/macro-goals', [Coach\MacroGoalController::class, 'store'])->name('clients.macro-goals.store');
        Route::delete('macro-goals/{macroGoal}', [Coach\MacroGoalController::class, 'destroy'])->name('macro-goals.destroy');

        Route::resource('exercises', Coach\ExerciseController::class);
        Route::resource('meals', Coach\MealController::class)->except(['show']);
        Route::resource('rewards', Coach\RewardController::class)->except(['show']);
        Route::resource('achievements', Coach\AchievementController::class)->except(['show']);
        Route::post('clients/{client}/achievements/{achievement}/award', [Coach\AchievementController::class, 'award'])->name('clients.achievements.award');

        Route::get('redemptions', [Coach\RedemptionController::class, 'index'])->name('redemptions.index');
        Route::patch('redemptions/{redemption}', [Coach\RedemptionController::class, 'update'])->name('redemptions.update');

        // Tracking metrics
        Route::get('tracking-metrics', [Coach\TrackingMetricController::class, 'index'])->name('tracking-metrics.index');
        Route::post('tracking-metrics', [Coach\TrackingMetricController::class, 'store'])->name('tracking-metrics.store');
        Route::put('tracking-metrics/{trackingMetric}', [Coach\TrackingMetricController::class, 'update'])->name('tracking-metrics.update');
        Route::delete('tracking-metrics/{trackingMetric}', [Coach\TrackingMetricController::class, 'destroy'])->name('tracking-metrics.destroy');
        Route::post('tracking-metrics/{trackingMetric}/restore', [Coach\TrackingMetricController::class, 'restore'])->name('tracking-metrics.restore');
        Route::post('tracking-metrics/{trackingMetric}/move-up', [Coach\TrackingMetricController::class, 'moveUp'])->name('tracking-metrics.move-up');
        Route::post('tracking-metrics/{trackingMetric}/move-down', [Coach\TrackingMetricController::class, 'moveDown'])->name('tracking-metrics.move-down');
        Route::get('messages', [Coach\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{user}', [Coach\MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{user}', [Coach\MessageController::class, 'store'])->name('messages.store');
        Route::get('messages/{user}/poll', [Coach\MessageController::class, 'poll'])->name('messages.poll');

        // Branding
        Route::get('branding', [Coach\BrandingController::class, 'edit'])->name('branding.edit');
        Route::put('branding', [Coach\BrandingController::class, 'update'])->name('branding.update');
    });

// Client routes
Route::middleware(['auth', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/', Client\DashboardController::class)->name('dashboard');
        Route::get('welcome', [Client\OnboardingController::class, 'welcome'])->name('welcome');
        Route::get('onboarding', [Client\OnboardingController::class, 'show'])->name('onboarding');
        Route::post('onboarding', [Client\OnboardingController::class, 'store'])->name('onboarding.store');
        Route::post('onboarding/skip', [Client\OnboardingController::class, 'skip'])->name('onboarding.skip');
        Route::get('check-in', [Client\CheckInController::class, 'index'])->name('check-in');
        Route::post('check-in', [Client\CheckInController::class, 'store'])->name('check-in.store');
        Route::get('program', [Client\ProgramController::class, 'index'])->name('program');
        Route::get('log', [Client\LogController::class, 'index'])->name('log');
        Route::get('log/custom', [Client\LogController::class, 'createCustom'])->name('log.custom');
        Route::get('log/exercises', [Client\LogController::class, 'exercises'])->name('log.exercises');
        Route::get('log/{workout}', [Client\LogController::class, 'create'])->name('log.create');
        Route::post('log', [Client\LogController::class, 'store'])->name('log.store');
        Route::get('history', [Client\HistoryController::class, 'index'])->name('history');
        Route::get('history/{workoutLog}', [Client\HistoryController::class, 'show'])->name('history.show');
        Route::post('history/{workoutLog}/comment', [Client\HistoryController::class, 'comment'])->name('history.comment');
        Route::get('nutrition', [Client\NutritionController::class, 'index'])->name('nutrition');
        Route::get('nutrition/meals', [Client\NutritionController::class, 'meals'])->name('nutrition.meals');
        Route::post('nutrition', [Client\NutritionController::class, 'store'])->name('nutrition.store');
        Route::delete('nutrition/{mealLog}', [Client\NutritionController::class, 'destroy'])->name('nutrition.destroy');
        Route::get('messages', [Client\MessageController::class, 'index'])->name('messages');
        Route::post('messages', [Client\MessageController::class, 'store'])->name('messages.store');
        Route::get('messages/poll', [Client\MessageController::class, 'poll'])->name('messages.poll');
        Route::get('achievements', [Client\AchievementController::class, 'index'])->name('achievements');
    });

// Media serving (private, authorized)
Route::middleware('auth')->group(function () {
    Route::get('media/daily-log/{dailyLog}/{conversion?}', [\App\Http\Controllers\MediaController::class, 'dailyLog'])->name('media.daily-log');
});

require __DIR__.'/auth.php';
