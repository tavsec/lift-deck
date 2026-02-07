<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMacroGoalRequest;
use App\Models\MacroGoal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class MacroGoalController extends Controller
{
    public function store(StoreMacroGoalRequest $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        MacroGoal::create([
            ...$request->validated(),
            'client_id' => $client->id,
            'coach_id' => auth()->id(),
        ]);

        return redirect()->route('coach.clients.nutrition', $client)
            ->with('success', 'Macro goal set successfully!');
    }

    public function destroy(MacroGoal $macroGoal): RedirectResponse
    {
        if ($macroGoal->coach_id !== auth()->id()) {
            abort(403);
        }

        $clientId = $macroGoal->client_id;
        $macroGoal->delete();

        return redirect()->route('coach.clients.nutrition', $clientId)
            ->with('success', 'Macro goal removed.');
    }
}
