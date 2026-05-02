<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMealLogCommentRequest;
use App\Models\MealLog;
use App\Models\MealLogComment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class MealLogCommentController extends Controller
{
    public function store(StoreMealLogCommentRequest $request, User $client, MealLog $mealLog): RedirectResponse
    {
        $validated = $request->validated();

        MealLogComment::create([
            'meal_log_id' => $mealLog->id,
            'author_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return redirect()->route('coach.clients.nutrition', [
            'client' => $client,
            'date' => $mealLog->date->format('Y-m-d'),
        ])->with('success', __('coach.clients.nutrition.comments.added'));
    }

    public function destroy(MealLogComment $comment): RedirectResponse
    {
        if ($comment->author_id !== auth()->id()) {
            abort(403);
        }

        $mealLog = $comment->mealLog;
        $comment->delete();

        return redirect()->route('coach.clients.nutrition', [
            'client' => $mealLog->client_id,
            'date' => $mealLog->date->format('Y-m-d'),
        ])->with('success', __('coach.clients.nutrition.comments.deleted'));
    }
}
