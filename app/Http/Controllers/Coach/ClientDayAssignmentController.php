<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientDayAssignmentRequest;
use App\Models\ClientDayAssignment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class ClientDayAssignmentController extends Controller
{
    /**
     * Assign a day plan to a client on a specific date.
     */
    public function store(StoreClientDayAssignmentRequest $request, User $client): RedirectResponse
    {
        $validated = $request->validated();

        $existing = ClientDayAssignment::query()
            ->where('client_id', $client->id)
            ->whereDate('date', $validated['date'])
            ->first();

        if ($existing instanceof ClientDayAssignment) {
            throw ValidationException::withMessages([
                'date' => __('coach.day_plans.assignments.duplicate_date'),
            ]);
        }

        ClientDayAssignment::create([
            'client_id' => $client->id,
            'coach_id' => auth()->id(),
            'day_plan_id' => $validated['day_plan_id'],
            'date' => $validated['date'],
        ]);

        return redirect()->route('coach.clients.nutrition', $client)
            ->with('success', __('coach.day_plans.flash.assigned'));
    }

    /**
     * Remove a day plan assignment from a client.
     */
    public function destroy(User $client, ClientDayAssignment $assignment): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        if ($assignment->client_id !== $client->id || $assignment->coach_id !== auth()->id()) {
            abort(403);
        }

        $assignment->delete();

        return redirect()->route('coach.clients.nutrition', $client)
            ->with('success', __('coach.day_plans.flash.unassigned'));
    }
}
