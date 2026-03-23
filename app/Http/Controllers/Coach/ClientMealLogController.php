<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientMealLogRequest;
use App\Models\Meal;
use App\Models\MealLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ClientMealLogController extends Controller
{
    public function store(StoreClientMealLogRequest $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        if (! empty($validated['meal_id'])) {
            $meal = Meal::findOrFail($validated['meal_id']);
            if ($meal->coach_id !== auth()->id()) {
                abort(403);
            }
        }

        MealLog::create([
            ...$validated,
            'client_id' => $client->id,
        ]);

        return redirect()->route('coach.clients.nutrition', [
            'client' => $client,
            'date' => $validated['date'],
        ])->with('success', 'Meal logged for client.');
    }

    public function destroy(User $client, MealLog $mealLog): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($mealLog->client_id !== $client->id) {
            abort(403);
        }

        $date = $mealLog->date->format('Y-m-d');
        $mealLog->delete();

        return redirect()->route('coach.clients.nutrition', [
            'client' => $client,
            'date' => $date,
        ])->with('success', 'Meal removed.');
    }
}
