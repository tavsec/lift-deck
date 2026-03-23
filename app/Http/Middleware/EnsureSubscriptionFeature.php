<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionFeature
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $coach = $request->user();

        if (! $coach || ! $this->subscriptionService->hasFeature($coach, $feature)) {
            return redirect()->route('coach.subscription')
                ->with('feature_required', $feature);
        }

        return $next($request);
    }
}
