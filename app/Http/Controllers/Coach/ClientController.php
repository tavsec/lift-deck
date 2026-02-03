<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClientInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request): View
    {
        $coach = auth()->user();

        $query = $coach->clients()->with('clientProfile');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(10);

        $pendingInvitations = $coach->sentInvitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();

        return view('coach.clients.index', compact('clients', 'pendingInvitations'));
    }

    /**
     * Show the form for inviting a new client.
     */
    public function create(): View
    {
        return view('coach.clients.create');
    }

    /**
     * Generate a new invitation code.
     */
    public function store(Request $request): RedirectResponse
    {
        $coach = auth()->user();

        $invitation = ClientInvitation::create([
            'coach_id' => $coach->id,
            'token' => ClientInvitation::generateUniqueToken(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('coach.clients.index')
            ->with('success', 'Invitation code generated!')
            ->with('invitation_code', $invitation->token);
    }

    /**
     * Display the specified client.
     */
    public function show(User $client): View
    {
        // Ensure this client belongs to the coach
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $client->load(['clientProfile', 'clientPrograms' => function ($query) {
            $query->active()->with('program');
        }]);

        $activeProgram = $client->clientPrograms->first();

        return view('coach.clients.show', compact('client', 'activeProgram'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(User $client): View
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $client->load('clientProfile');

        return view('coach.clients.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $client->update($validated);

        return redirect()->route('coach.clients.show', $client)
            ->with('success', 'Client updated successfully!');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(User $client): RedirectResponse
    {
        if ($client->coach_id !== auth()->id()) {
            abort(403);
        }

        $client->delete();

        return redirect()->route('coach.clients.index')
            ->with('success', 'Client removed successfully.');
    }
}
