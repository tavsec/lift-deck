<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Pennant\Feature;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureActive
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        $coach = $user->isCoach() ? $user : $user->coach;

        if (! $coach || Feature::for($coach)->inactive($feature)) {
            abort(403);
        }

        return $next($request);
    }
}
