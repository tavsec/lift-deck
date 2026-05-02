<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MealLogComment;
use Illuminate\Http\JsonResponse;

class MealLogCommentController extends Controller
{
    /**
     * Mark all unread meal-log comments on the authenticated client's
     * meal logs as read.
     */
    public function markAllRead(): JsonResponse
    {
        $clientId = auth()->id();

        $updated = MealLogComment::query()
            ->whereNull('read_at')
            ->whereHas('mealLog', fn ($q) => $q->where('client_id', $clientId))
            ->update(['read_at' => now()]);

        return response()->json([
            'updated' => $updated,
        ]);
    }
}
