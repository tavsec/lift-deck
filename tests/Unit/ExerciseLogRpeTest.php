<?php

use App\Models\ExerciseLog;

it('accepts rpe in fillable', function () {
    $log = new ExerciseLog(['rpe' => 7]);
    expect($log->rpe)->toBe(7);
});

it('casts rpe as integer', function () {
    $log = new ExerciseLog(['rpe' => '8']);
    expect($log->rpe)->toBeInt();
});
