<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function dailyLog(DailyLog $dailyLog, ?string $conversion = null): Response
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
        if ($conversion && in_array($conversion, $validConversions)) {
            $path = $media->getPath($conversion);
        } else {
            $path = $media->getPath();
        }

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $media->mime_type,
        ]);
    }
}
