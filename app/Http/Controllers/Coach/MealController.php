<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Models\Meal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MealController extends Controller
{
    /**
     * Display a listing of meals.
     */
    public function index(Request $request): View
    {
        $coach = auth()->user();

        $query = $coach->meals()->active();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $meals = $query->orderBy('name')->paginate(20);

        return view('coach.meals.index', compact('meals'));
    }

    /**
     * Show the form for creating a new meal.
     */
    public function create(): View
    {
        return view('coach.meals.create');
    }

    /**
     * Store a newly created meal.
     */
    public function store(StoreMealRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['coach_id'] = auth()->id();

        Meal::create($validated);

        return redirect()->route('coach.meals.index')
            ->with('success', 'Meal created successfully!');
    }

    /**
     * Show the form for editing the specified meal.
     */
    public function edit(Meal $meal): View
    {
        $this->authorizeMeal($meal);

        return view('coach.meals.edit', compact('meal'));
    }

    /**
     * Update the specified meal.
     */
    public function update(UpdateMealRequest $request, Meal $meal): RedirectResponse
    {
        $this->authorizeMeal($meal);

        $meal->update($request->validated());

        return redirect()->route('coach.meals.index')
            ->with('success', 'Meal updated successfully!');
    }

    /**
     * Archive the specified meal.
     */
    public function destroy(Meal $meal): RedirectResponse
    {
        $this->authorizeMeal($meal);

        $meal->update(['is_active' => false]);

        return redirect()->route('coach.meals.index')
            ->with('success', 'Meal archived successfully.');
    }

    /**
     * Authorize that the coach owns this meal.
     */
    private function authorizeMeal(Meal $meal): void
    {
        if ($meal->coach_id !== auth()->id()) {
            abort(403);
        }
    }
}
