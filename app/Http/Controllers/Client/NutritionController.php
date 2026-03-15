<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealLogRequest;
use App\Jobs\ProcessXpEvent;
use App\Models\MacroGoal;
use App\Models\Meal;
use App\Models\MealLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NutritionController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $date = $request->get('date', now()->format('Y-m-d'));

        $macroGoal = MacroGoal::activeForClient($user->id, $date);

        $mealLogs = $user->mealLogs()
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $totals = [
            'calories' => $mealLogs->sum('calories'),
            'protein' => $mealLogs->sum('protein'),
            'carbs' => $mealLogs->sum('carbs'),
            'fat' => $mealLogs->sum('fat'),
        ];

        return view('client.nutrition', compact('date', 'macroGoal', 'mealLogs', 'totals'));
    }

    public function store(StoreMealLogRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validated();

        if (! empty($validated['meal_id'])) {
            $meal = Meal::findOrFail($validated['meal_id']);
            if ($meal->coach_id !== $user->coach_id) {
                abort(403);
            }
        }

        $mealLog = MealLog::create([
            ...$validated,
            'client_id' => $user->id,
        ]);

        ProcessXpEvent::dispatch(auth()->id(), 'meal_logged', ['meal_log_id' => $mealLog->id]);

        return redirect()->route('client.nutrition', ['date' => $validated['date']])
            ->with('success', 'Meal logged!');
    }

    public function destroy(MealLog $mealLog): RedirectResponse
    {
        if ($mealLog->client_id !== auth()->id()) {
            abort(403);
        }

        $date = $mealLog->date->format('Y-m-d');
        $mealLog->delete();

        return redirect()->route('client.nutrition', ['date' => $date])
            ->with('success', 'Meal removed.');
    }

    public function meals(Request $request): JsonResponse
    {
        $user = auth()->user();

        $meals = Meal::where('coach_id', $user->coach_id)
            ->active()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('name')
            ->get(['id', 'name', 'calories', 'protein', 'carbs', 'fat']);

        return response()->json($meals);
    }
}
