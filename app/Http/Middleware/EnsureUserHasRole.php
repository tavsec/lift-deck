<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            if ($request->user()?->isCoach()) {
                return redirect()->route('coach.dashboard');
            }

            if ($request->user()?->isClient()) {
                return redirect()->route('client.dashboard');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
