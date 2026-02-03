<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $activeProgram = $user->activeProgram()?->load('program.workouts.exercises.exercise');

        return view('client.program', [
            'activeProgram' => $activeProgram,
        ]);
    }
}
