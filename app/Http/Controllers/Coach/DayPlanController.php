<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDayPlanRequest;
use App\Http\Requests\UpdateDayPlanRequest;
use App\Models\DayPlan;
use App\Models\User;
use App\Services\OpenFoodFacts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DayPlanController extends Controller
{
    public function __construct(private readonly OpenFoodFacts $openFoodFacts) {}

    /**
     * Show the form for creating a new day plan for the given client.
     */
    public function create(User $client): View
    {
        $this->authorizeClient($client);

        $coach = auth()->user();

        $availableMeals = $coach->meals()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'calories', 'protein', 'carbs', 'fat']);

        $defaultSections = ['Breakfast', 'Lunch', 'Dinner', 'Snack'];

        return view('coach.day-plans.create', compact('client', 'availableMeals', 'defaultSections'));
    }

    /**
     * Store a newly created day plan for the given client.
     */
    public function store(StoreDayPlanRequest $request, User $client): RedirectResponse
    {
        $validated = $request->validated();

        $dayPlan = DB::transaction(function () use ($validated, $client): DayPlan {
            $dayPlan = DayPlan::create([
                'coach_id' => auth()->id(),
                'client_id' => $client->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            $this->syncItems($dayPlan, $validated['items'] ?? []);

            return $dayPlan;
        });

        return redirect()->route('coach.clients.nutrition', $client)
            ->with('success', __('coach.day_plans.flash.created'))
            ->with('ga_event', ['name' => 'day_plan_created', 'params' => ['day_plan_id' => $dayPlan->id]]);
    }

    /**
     * Show the form for editing the specified day plan.
     */
    public function edit(User $client, DayPlan $dayPlan): View
    {
        $this->authorizeClient($client);
        $this->authorizeDayPlan($client, $dayPlan);

        $coach = auth()->user();

        $dayPlan->load('items');

        $availableMeals = $coach->meals()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'calories', 'protein', 'carbs', 'fat']);

        $defaultSections = ['Breakfast', 'Lunch', 'Dinner', 'Snack'];

        return view('coach.day-plans.edit', compact('client', 'dayPlan', 'availableMeals', 'defaultSections'));
    }

    /**
     * Update the specified day plan.
     */
    public function update(UpdateDayPlanRequest $request, User $client, DayPlan $dayPlan): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($dayPlan, $validated): void {
            $dayPlan->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $dayPlan->items()->delete();

            $this->syncItems($dayPlan, $validated['items'] ?? []);
        });

        return redirect()->route('coach.clients.nutrition', $client)
            ->with('success', __('coach.day_plans.flash.updated'));
    }

    /**
     * Archive the specified day plan.
     */
    public function destroy(User $client, DayPlan $dayPlan): RedirectResponse
    {
        $this->authorizeClient($client);
        $this->authorizeDayPlan($client, $dayPlan);

        $dayPlan->update(['is_active' => false]);

        return redirect()->route('coach.clients.nutrition', $client)
            ->with('success', __('coach.day_plans.flash.archived'));
    }

    /**
     * Open Food Facts proxy search for the day-plan builder.
     */
    public function foodSearch(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $results = $this->openFoodFacts->search($query);

        return response()->json(['results' => $results->values()]);
    }

    /**
     * Persist a list of day-plan item rows by snapshotting all required fields.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    private function syncItems(DayPlan $dayPlan, array $items): void
    {
        $sortBySection = [];

        foreach ($items as $index => $item) {
            $source = $item['source'] ?? 'custom';
            $section = (string) ($item['meal_type'] ?? '');
            if ($section === '') {
                continue;
            }

            $sortBySection[$section] = ($sortBySection[$section] ?? -1) + 1;

            $dayPlan->items()->create([
                'meal_id' => $source === 'library' ? ($item['meal_id'] ?? null) : null,
                'off_code' => $source === 'off' ? ($item['off_code'] ?? null) : null,
                'meal_type' => $section,
                'name' => (string) $item['name'],
                'calories' => (int) $item['calories'],
                'protein' => (float) $item['protein'],
                'carbs' => (float) $item['carbs'],
                'fat' => (float) $item['fat'],
                'portion_grams' => isset($item['portion_grams']) && $item['portion_grams'] !== '' && $item['portion_grams'] !== null
                    ? (int) $item['portion_grams']
                    : null,
                'sort_order' => $item['sort_order'] ?? $sortBySection[$section],
            ]);
        }
    }

    /**
     * Ensure the route-bound client belongs to the authenticated coach.
     */
    private function authorizeClient(User $client): void
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }
    }

    /**
     * Ensure the day plan belongs to the auth coach AND the route-bound client.
     */
    private function authorizeDayPlan(User $client, DayPlan $dayPlan): void
    {
        if ($dayPlan->coach_id !== auth()->id() || $dayPlan->client_id !== $client->id) {
            abort(403);
        }
    }
}
