<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCoachSubscribed
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $coach = $request->user();

        if (! $coach) {
            return $next($request);
        }

        if ($request->routeIs('coach.subscription') || $request->routeIs('coach.subscription.*')) {
            return $next($request);
        }

        $isActive = $this->subscriptionService->isActive($coach);
        $isInGracePeriod = $this->subscriptionService->isInGracePeriod($coach);

        if (! $isActive && ! $isInGracePeriod) {
            return redirect()->route('coach.subscription');
        }

        if ($isInGracePeriod) {
            $daysRemaining = $this->subscriptionService->graceDaysRemaining($coach);
            session()->flash('subscription_grace_days', $daysRemaining);
        }

        return $next($request);
    }
}
