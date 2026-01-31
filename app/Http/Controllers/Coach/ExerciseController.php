<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises.
     */
    public function index(Request $request): View
    {
        $coach = auth()->user();

        $query = Exercise::query()
            ->forCoach($coach->id)
            ->active();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($muscleGroup = $request->get('muscle_group')) {
            $query->where('muscle_group', $muscleGroup);
        }

        $exercises = $query->orderBy('name')->paginate(20);

        $muscleGroups = Exercise::query()
            ->forCoach($coach->id)
            ->active()
            ->distinct()
            ->pluck('muscle_group')
            ->sort()
            ->values();

        return view('coach.exercises.index', compact('exercises', 'muscleGroups'));
    }

    /**
     * Show the form for creating a new exercise.
     */
    public function create(): View
    {
        $muscleGroups = $this->getMuscleGroupOptions();

        return view('coach.exercises.create', compact('muscleGroups'));
    }

    /**
     * Store a newly created exercise.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'muscle_group' => ['required', 'string', 'max:100'],
            'video_url' => ['nullable', 'url', 'max:500'],
        ]);

        $validated['coach_id'] = auth()->id();

        Exercise::create($validated);

        return redirect()->route('coach.exercises.index')
            ->with('success', 'Exercise created successfully!');
    }

    /**
     * Display the specified exercise.
     */
    public function show(Exercise $exercise): View
    {
        $this->authorizeExercise($exercise);

        return view('coach.exercises.show', compact('exercise'));
    }

    /**
     * Show the form for editing the specified exercise.
     */
    public function edit(Exercise $exercise): View
    {
        $this->authorizeExercise($exercise);

        if ($exercise->isGlobal()) {
            abort(403, 'Cannot edit global exercises.');
        }

        $muscleGroups = $this->getMuscleGroupOptions();

        return view('coach.exercises.edit', compact('exercise', 'muscleGroups'));
    }

    /**
     * Update the specified exercise.
     */
    public function update(Request $request, Exercise $exercise): RedirectResponse
    {
        $this->authorizeExercise($exercise);

        if ($exercise->isGlobal()) {
            abort(403, 'Cannot edit global exercises.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'muscle_group' => ['required', 'string', 'max:100'],
            'video_url' => ['nullable', 'url', 'max:500'],
        ]);

        $exercise->update($validated);

        return redirect()->route('coach.exercises.show', $exercise)
            ->with('success', 'Exercise updated successfully!');
    }

    /**
     * Remove the specified exercise.
     */
    public function destroy(Exercise $exercise): RedirectResponse
    {
        $this->authorizeExercise($exercise);

        if ($exercise->isGlobal()) {
            abort(403, 'Cannot delete global exercises.');
        }

        $exercise->delete();

        return redirect()->route('coach.exercises.index')
            ->with('success', 'Exercise deleted successfully.');
    }

    /**
     * Authorize that the coach can access this exercise.
     */
    private function authorizeExercise(Exercise $exercise): void
    {
        if (! $exercise->isGlobal() && $exercise->coach_id !== auth()->id()) {
            abort(403);
        }
    }

    /**
     * Get common muscle group options.
     */
    private function getMuscleGroupOptions(): array
    {
        return [
            'chest' => 'Chest',
            'back' => 'Back',
            'shoulders' => 'Shoulders',
            'biceps' => 'Biceps',
            'triceps' => 'Triceps',
            'forearms' => 'Forearms',
            'core' => 'Core',
            'quadriceps' => 'Quadriceps',
            'hamstrings' => 'Hamstrings',
            'glutes' => 'Glutes',
            'calves' => 'Calves',
            'full_body' => 'Full Body',
            'cardio' => 'Cardio',
        ];
    }
}
