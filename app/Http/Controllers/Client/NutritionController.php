<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealLogRequest;
use App\Jobs\ProcessXpEvent;
use App\Models\ClientDayAssignment;
use App\Models\DayPlanItem;
use App\Models\MacroGoal;
use App\Models\Meal;
use App\Models\MealLog;
use App\Models\MealLogComment;
use App\Services\AnalyticsService;
use App\Services\OpenFoodFacts;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NutritionController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analyticsService,
        private readonly OpenFoodFacts $openFoodFacts,
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
        $date = $request->get('date', now()->format('Y-m-d'));

        $macroGoal = MacroGoal::activeForClient($user->id, $date);

        $mealLogs = $user->mealLogs()
            ->with('comments.author:id,name')
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $unreadCommentCount = MealLogComment::query()
            ->whereNull('read_at')
            ->whereHas('mealLog', fn ($q) => $q->where('client_id', $user->id))
            ->count();

        $totals = [
            'calories' => $mealLogs->sum('calories'),
            'protein' => $mealLogs->sum('protein'),
            'carbs' => $mealLogs->sum('carbs'),
            'fat' => $mealLogs->sum('fat'),
        ];

        $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');
        $hasPreviousDayLogs = $mealLogs->isEmpty()
            && MealLog::query()
                ->where('client_id', $user->id)
                ->whereDate('date', $previousDate)
                ->exists();

        $favorites = $this->favoritesForClient($user->id);

        $assignment = ClientDayAssignment::query()
            ->where('client_id', $user->id)
            ->whereDate('date', $date)
            ->with(['dayPlan.items.meal'])
            ->first();

        $assignedItems = $this->buildAssignedItems($assignment, $user->id, $date);

        $from = now()->subDays(29)->format('Y-m-d');
        $to = now()->format('Y-m-d');
        [
            'nutritionData' => $nutritionData,
            'nutritionStats' => $nutritionStats,
        ] = $this->analyticsService->getNutritionData($user, $from, $to);

        return view('client.nutrition', compact(
            'date',
            'macroGoal',
            'mealLogs',
            'totals',
            'nutritionData',
            'nutritionStats',
            'hasPreviousDayLogs',
            'favorites',
            'assignment',
            'assignedItems',
            'unreadCommentCount',
        ));
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

        if (! empty($validated['day_plan_item_id'])) {
            $item = DayPlanItem::with('dayPlan')->findOrFail($validated['day_plan_item_id']);
            if ($item->dayPlan->coach_id !== $user->coach_id) {
                abort(403);
            }
        }

        $mealLog = MealLog::create([
            ...$validated,
            'client_id' => $user->id,
        ]);

        ProcessXpEvent::dispatch(auth()->id(), 'meal_logged', ['meal_log_id' => $mealLog->id]);

        return redirect()->route('client.nutrition', ['date' => $validated['date']])
            ->with('success', 'Meal logged!')
            ->with('ga_event', ['name' => 'meal_logged']);
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

    public function foodSearch(Request $request): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $results = $this->openFoodFacts->search($query);

        return response()->json(['results' => $results->values()]);
    }

    public function copyYesterday(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $currentDate = $request->input('date', now()->format('Y-m-d'));
        $previousDate = Carbon::parse($currentDate)->subDay()->format('Y-m-d');

        $previousLogs = MealLog::query()
            ->where('client_id', $user->id)
            ->whereDate('date', $previousDate)
            ->orderBy('created_at')
            ->get();

        if ($previousLogs->isEmpty()) {
            return redirect()->route('client.nutrition', ['date' => $currentDate])
                ->with('error', __('client.nutrition.quick_log.nothing_to_copy'));
        }

        $count = DB::transaction(function () use ($previousLogs, $user, $currentDate): int {
            $created = 0;
            foreach ($previousLogs as $log) {
                MealLog::create([
                    'client_id' => $user->id,
                    'meal_id' => $log->meal_id,
                    'date' => $currentDate,
                    'meal_type' => $log->meal_type,
                    'name' => $log->name,
                    'calories' => $log->calories,
                    'protein' => $log->protein,
                    'carbs' => $log->carbs,
                    'fat' => $log->fat,
                    'notes' => $log->notes,
                ]);
                $created++;
            }

            return $created;
        });

        return redirect()->route('client.nutrition', ['date' => $currentDate])
            ->with('success', __('client.nutrition.quick_log.copied', ['count' => $count]));
    }

    /**
     * Build a list of up to 5 favorite meals for the client based on
     * frequency over the last 90 days.
     *
     * @return \Illuminate\Support\Collection<int, array{name: string, calories: int, protein: float, carbs: float, fat: float, meal_id: int|null}>
     */
    private function favoritesForClient(int $clientId): Collection
    {
        $since = now()->subDays(90)->format('Y-m-d');

        $logs = MealLog::query()
            ->where('client_id', $clientId)
            ->whereDate('date', '>=', $since)
            ->orderByDesc('created_at')
            ->get(['id', 'meal_id', 'name', 'calories', 'protein', 'carbs', 'fat']);

        return $logs
            ->groupBy(fn (MealLog $log): string => mb_strtolower(trim((string) $log->name)))
            ->filter(fn ($_group, $key): bool => $key !== '')
            ->map(function ($group): array {
                /** @var MealLog $latest */
                $latest = $group->first();

                return [
                    'name' => $latest->name,
                    'calories' => (int) $latest->calories,
                    'protein' => (float) $latest->protein,
                    'carbs' => (float) $latest->carbs,
                    'fat' => (float) $latest->fat,
                    'meal_id' => $latest->meal_id,
                    'frequency' => $group->count(),
                ];
            })
            ->sortByDesc('frequency')
            ->take(5)
            ->map(function (array $item): array {
                unset($item['frequency']);

                return $item;
            })
            ->values();
    }

    /**
     * Build a collection of assigned plan items with completion state for the client.
     *
     * @return \Illuminate\Support\Collection<int, array{item: \App\Models\DayPlanItem, completed: bool}>
     */
    private function buildAssignedItems(?ClientDayAssignment $assignment, int $clientId, string $date): Collection
    {
        if (! $assignment instanceof ClientDayAssignment || $assignment->dayPlan === null) {
            return collect();
        }

        $itemIds = $assignment->dayPlan->items->pluck('id')->all();

        $loggedItemIds = MealLog::query()
            ->where('client_id', $clientId)
            ->whereDate('date', $date)
            ->whereIn('day_plan_item_id', $itemIds)
            ->pluck('day_plan_item_id')
            ->all();

        $loggedSet = array_flip($loggedItemIds);

        return $assignment->dayPlan->items->map(fn (DayPlanItem $item): array => [
            'item' => $item,
            'completed' => isset($loggedSet[$item->id]),
        ])->values();
    }
}
