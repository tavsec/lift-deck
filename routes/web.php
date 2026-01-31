<?php

use App\Http\Controllers\Client;
use App\Http\Controllers\Coach;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Coach routes
Route::middleware(['auth', 'verified', 'role:coach'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('/', Coach\DashboardController::class)->name('dashboard');
        Route::resource('clients', Coach\ClientController::class);
        Route::resource('programs', Coach\ProgramController::class);
        Route::resource('exercises', Coach\ExerciseController::class);
        Route::get('messages', [Coach\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{user}', [Coach\MessageController::class, 'show'])->name('messages.show');
    });

// Client routes
Route::middleware(['auth', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/', Client\DashboardController::class)->name('dashboard');
        Route::get('program', [Client\ProgramController::class, 'index'])->name('program');
        Route::get('log', [Client\LogController::class, 'index'])->name('log');
        Route::get('history', [Client\HistoryController::class, 'index'])->name('history');
        Route::get('messages', [Client\MessageController::class, 'index'])->name('messages');
    });

require __DIR__.'/auth.php';
