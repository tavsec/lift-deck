<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserPreferencesController extends Controller
{
    /**
     * Toggle the authenticated user's dark mode preference.
     */
    public function toggleDarkMode(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['dark_mode' => ! $user->dark_mode]);

        return back();
    }
}
