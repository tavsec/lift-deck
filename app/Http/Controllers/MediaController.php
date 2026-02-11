<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Illuminate\Http\RedirectResponse;

class MediaController extends Controller
{
    public function dailyLog(DailyLog $dailyLog, ?string $conversion = null): RedirectResponse
    {
        $user = auth()->user();
        $isOwner = $dailyLog->client_id === $user->id;
        $isCoach = $dailyLog->client->coach_id === $user->id;

        if (! $isOwner && ! $isCoach) {
            abort(403);
        }

        $media = $dailyLog->getFirstMedia('check-in-image');

        if (! $media) {
            abort(404);
        }

        $validConversions = ['thumb', 'full'];
        $conversionName = ($conversion && in_array($conversion, $validConversions)) ? $conversion : '';

        $temporaryUrl = $media->getTemporaryUrl(now()->addMinutes(5), $conversionName);

        return redirect($temporaryUrl);
    }
}
