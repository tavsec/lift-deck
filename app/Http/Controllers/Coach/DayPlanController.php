<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDayPlanRequest;
use App\Http\Requests\UpdateDayPlanRequest;
use App\Models\DayPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DayPlanController extends Controller
{
    /**
     * Display a listing of day plans.
     */
    public function index(): View
    {
        $coach = auth()->user();

        $dayPlans = $coach->dayPlans()
            ->active()
            ->with('items.meal')
            ->orderBy('name')
            ->get();

        return view('coach.day-plans.index', compact('dayPlans'));
    }

    /**
     * Show the form for creating a new day plan.
     */
    public function create(): View
    {
        $coach = auth()->user();

        $availableMeals = $coach->meals()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'calories', 'protein', 'carbs', 'fat']);

        return view('coach.day-plans.create', compact('availableMeals'));
    }

    /**
     * Store a newly created day plan.
     */
    public function store(StoreDayPlanRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $dayPlan = DB::transaction(function () use ($validated): DayPlan {
            $dayPlan = DayPlan::create([
                'coach_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            foreach ($validated['items'] ?? [] as $index => $item) {
                $dayPlan->items()->create([
                    'meal_id' => $item['meal_id'],
                    'meal_type' => $item['meal_type'],
                    'sort_order' => $item['sort_order'] ?? $index,
                ]);
            }

            return $dayPlan;
        });

        return redirect()->route('coach.day-plans.index')
            ->with('success', __('coach.day_plans.flash.created'))
            ->with('ga_event', ['name' => 'day_plan_created', 'params' => ['day_plan_id' => $dayPlan->id]]);
    }

    /**
     * Show the form for editing the specified day plan.
     */
    public function edit(DayPlan $dayPlan): View
    {
        $this->authorizeDayPlan($dayPlan);

        $coach = auth()->user();

        $dayPlan->load('items.meal');

        $availableMeals = $coach->meals()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'calories', 'protein', 'carbs', 'fat']);

        return view('coach.day-plans.edit', compact('dayPlan', 'availableMeals'));
    }

    /**
     * Update the specified day plan.
     */
    public function update(UpdateDayPlanRequest $request, DayPlan $dayPlan): RedirectResponse
    {
        $this->authorizeDayPlan($dayPlan);

        $validated = $request->validated();

        DB::transaction(function () use ($dayPlan, $validated): void {
            $dayPlan->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $dayPlan->items()->delete();

            foreach ($validated['items'] ?? [] as $index => $item) {
                $dayPlan->items()->create([
                    'meal_id' => $item['meal_id'],
                    'meal_type' => $item['meal_type'],
                    'sort_order' => $item['sort_order'] ?? $index,
                ]);
            }
        });

        return redirect()->route('coach.day-plans.index')
            ->with('success', __('coach.day_plans.flash.updated'));
    }

    /**
     * Archive the specified day plan.
     */
    public function destroy(DayPlan $dayPlan): RedirectResponse
    {
        $this->authorizeDayPlan($dayPlan);

        $dayPlan->update(['is_active' => false]);

        return redirect()->route('coach.day-plans.index')
            ->with('success', __('coach.day_plans.flash.archived'));
    }

    /**
     * Authorize that the coach owns this day plan.
     */
    private function authorizeDayPlan(DayPlan $dayPlan): void
    {
        if ($dayPlan->coach_id !== auth()->id()) {
            abort(403);
        }
    }
}
