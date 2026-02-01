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

        Route::resource('exercises', Coach\ExerciseController::class);
        Route::get('messages', [Coach\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{user}', [Coach\MessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{user}', [Coach\MessageController::class, 'store'])->name('messages.store');
        Route::get('messages/{user}/poll', [Coach\MessageController::class, 'poll'])->name('messages.poll');
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
        Route::get('program', [Client\ProgramController::class, 'index'])->name('program');
        Route::get('log', [Client\LogController::class, 'index'])->name('log');
        Route::get('history', [Client\HistoryController::class, 'index'])->name('history');
        Route::get('messages', [Client\MessageController::class, 'index'])->name('messages');
        Route::post('messages', [Client\MessageController::class, 'store'])->name('messages.store');
        Route::get('messages/poll', [Client\MessageController::class, 'poll'])->name('messages.poll');
    });

require __DIR__.'/auth.php';
