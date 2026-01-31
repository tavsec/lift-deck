<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClientProgram;
use App\Models\Exercise;
use App\Models\Program;
use App\Models\ProgramWorkout;
use App\Models\User;
use App\Models\WorkoutExercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    /**
     * Display a listing of programs.
     */
    public function index(Request $request): View
    {
        $coach = auth()->user();

        $query = $coach->programs()->with('workouts.exercises');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($request->get('templates_only')) {
            $query->templates();
        }

        $programs = $query->latest()->paginate(12);

        return view('coach.programs.index', compact('programs'));
    }

    /**
     * Show the form for creating a new program.
     */
    public function create(): View
    {
        $typeOptions = (new Program)->getTypeOptions();

        return view('coach.programs.create', compact('typeOptions'));
    }

    /**
     * Store a newly created program.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_weeks' => ['nullable', 'integer', 'min:1', 'max:52'],
            'type' => ['required', 'string', 'in:strength,hypertrophy,fat_loss,general'],
            'is_template' => ['boolean'],
        ]);

        $validated['coach_id'] = auth()->id();
        $validated['is_template'] = $request->boolean('is_template');

        $program = Program::create($validated);

        return redirect()->route('coach.programs.edit', $program)
            ->with('success', 'Program created! Now add workouts and exercises.');
    }

    /**
     * Display the specified program.
     */
    public function show(Program $program): View
    {
        $this->authorizeProgram($program);

        $program->load('workouts.exercises.exercise');

        $assignedClients = ClientProgram::where('program_id', $program->id)
            ->with('client')
            ->get();

        return view('coach.programs.show', compact('program', 'assignedClients'));
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(Program $program): View
    {
        $this->authorizeProgram($program);

        $program->load('workouts.exercises.exercise');

        $typeOptions = (new Program)->getTypeOptions();
        $coach = auth()->user();
        $exercises = Exercise::forCoach($coach->id)->active()->orderBy('name')->get();

        return view('coach.programs.edit', compact('program', 'typeOptions', 'exercises'));
    }

    /**
     * Update the specified program.
     */
    public function update(Request $request, Program $program): RedirectResponse
    {
        $this->authorizeProgram($program);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_weeks' => ['nullable', 'integer', 'min:1', 'max:52'],
            'type' => ['required', 'string', 'in:strength,hypertrophy,fat_loss,general'],
            'is_template' => ['boolean'],
        ]);

        $validated['is_template'] = $request->boolean('is_template');

        $program->update($validated);

        return redirect()->route('coach.programs.show', $program)
            ->with('success', 'Program updated successfully!');
    }

    /**
     * Remove the specified program.
     */
    public function destroy(Program $program): RedirectResponse
    {
        $this->authorizeProgram($program);

        $program->delete();

        return redirect()->route('coach.programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Add a workout to the program.
     */
    public function addWorkout(Request $request, Program $program): RedirectResponse
    {
        $this->authorizeProgram($program);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'day_number' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $maxOrder = $program->workouts()->max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        $program->workouts()->create($validated);

        return redirect()->route('coach.programs.edit', $program)
            ->with('success', 'Workout added successfully!');
    }

    /**
     * Update a workout.
     */
    public function updateWorkout(Request $request, Program $program, ProgramWorkout $workout): RedirectResponse
    {
        $this->authorizeProgram($program);

        if ($workout->program_id !== $program->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'day_number' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $workout->update($validated);

        return redirect()->route('coach.programs.edit', $program)
            ->with('success', 'Workout updated successfully!');
    }

    /**
     * Delete a workout.
     */
    public function deleteWorkout(Program $program, ProgramWorkout $workout): RedirectResponse
    {
        $this->authorizeProgram($program);

        if ($workout->program_id !== $program->id) {
            abort(403);
        }

        $workout->delete();

        return redirect()->route('coach.programs.edit', $program)
            ->with('success', 'Workout deleted successfully.');
    }

    /**
     * Add an exercise to a workout.
     */
    public function addExercise(Request $request, Program $program, ProgramWorkout $workout): RedirectResponse
    {
        $this->authorizeProgram($program);

        if ($workout->program_id !== $program->id) {
            abort(403);
        }

        $validated = $request->validate([
            'exercise_id' => ['required', 'exists:exercises,id'],
            'sets' => ['required', 'integer', 'min:1', 'max:20'],
            'reps' => ['required', 'string', 'max:20'],
            'rest_seconds' => ['nullable', 'integer', 'min:0', 'max:600'],
            'notes' => ['nullable', 'string'],
        ]);

        $maxOrder = $workout->exercises()->max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        $workout->exercises()->create($validated);

        return redirect()->route('coach.programs.edit', $program)
            ->with('success', 'Exercise added successfully!');
    }

    /**
     * Update an exercise in a workout.
     */
    public function updateExercise(Request $request, Program $program, WorkoutExercise $workoutExercise): RedirectResponse
    {
        $this->authorizeProgram($program);

        if ($workoutExercise->programWorkout->program_id !== $program->id) {
            abort(403);
        }

        $validated = $request->validate([
            'sets' => ['required', 'integer', 'min:1', 'max:20'],
            'reps' => ['required', 'string', 'max:20'],
            'rest_seconds' => ['nullable', 'integer', 'min:0', 'max:600'],
            'notes' => ['nullable', 'string'],
        ]);

        $workoutExercise->update($validated);

        return redirect()->route('coach.programs.edit', $program)
            ->with('success', 'Exercise updated successfully!');
    }

    /**
     * Delete an exercise from a workout.
     */
    public function deleteExercise(Program $program, WorkoutExercise $workoutExercise): RedirectResponse
    {
        $this->authorizeProgram($program);

        if ($workoutExercise->programWorkout->program_id !== $program->id) {
            abort(403);
        }

        $workoutExercise->delete();

        return redirect()->route('coach.programs.edit', $program)
            ->with('success', 'Exercise removed successfully.');
    }

    /**
     * Move an exercise up in the order.
     */
    public function moveExerciseUp(Program $program, WorkoutExercise $workoutExercise): RedirectResponse
    {
        $this->authorizeProgram($program);

        if ($workoutExercise->programWorkout->program_id !== $program->id) {
            abort(403);
        }

        $previousExercise = WorkoutExercise::where('program_workout_id', $workoutExercise->program_workout_id)
            ->where('order', '<', $workoutExercise->order)
            ->orderBy('order', 'desc')
            ->first();

        if ($previousExercise) {
            $tempOrder = $workoutExercise->order;
            $workoutExercise->update(['order' => $previousExercise->order]);
            $previousExercise->update(['order' => $tempOrder]);
        }

        return redirect()->route('coach.programs.edit', $program);
    }

    /**
     * Move an exercise down in the order.
     */
    public function moveExerciseDown(Program $program, WorkoutExercise $workoutExercise): RedirectResponse
    {
        $this->authorizeProgram($program);

        if ($workoutExercise->programWorkout->program_id !== $program->id) {
            abort(403);
        }

        $nextExercise = WorkoutExercise::where('program_workout_id', $workoutExercise->program_workout_id)
            ->where('order', '>', $workoutExercise->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextExercise) {
            $tempOrder = $workoutExercise->order;
            $workoutExercise->update(['order' => $nextExercise->order]);
            $nextExercise->update(['order' => $tempOrder]);
        }

        return redirect()->route('coach.programs.edit', $program);
    }

    /**
     * Show the form to assign a program to a client.
     */
    public function assignForm(Program $program): View
    {
        $this->authorizeProgram($program);

        $coach = auth()->user();
        $clients = $coach->clients()
            ->whereDoesntHave('clientPrograms', function ($query) use ($program) {
                $query->where('program_id', $program->id)->where('status', 'active');
            })
            ->get();

        return view('coach.programs.assign', compact('program', 'clients'));
    }

    /**
     * Assign a program to a client.
     */
    public function assign(Request $request, Program $program): RedirectResponse
    {
        $this->authorizeProgram($program);

        $validated = $request->validate([
            'client_id' => ['required', 'exists:users,id'],
            'started_at' => ['required', 'date'],
        ]);

        $client = User::findOrFail($validated['client_id']);

        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        // Pause any existing active programs for this client
        ClientProgram::where('client_id', $client->id)
            ->where('status', 'active')
            ->update(['status' => 'paused']);

        ClientProgram::create([
            'client_id' => $client->id,
            'program_id' => $program->id,
            'started_at' => $validated['started_at'],
            'status' => 'active',
        ]);

        return redirect()->route('coach.programs.show', $program)
            ->with('success', "Program assigned to {$client->name}!");
    }

    /**
     * Authorize that the coach owns this program.
     */
    private function authorizeProgram(Program $program): void
    {
        if ($program->coach_id !== auth()->id()) {
            abort(403);
        }
    }
}
