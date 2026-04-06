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

        // Allow plan selection and checkout routes through unconditionally
        if ($request->routeIs('coach.plan', 'coach.plan.*', 'coach.subscription', 'coach.subscription.*')) {
            return $next($request);
        }

        $isActive = $this->subscriptionService->isActive($coach);
        $isInGracePeriod = $this->subscriptionService->isInGracePeriod($coach);

        if (! $isActive && ! $isInGracePeriod) {
            // Coach has never chosen a plan — send to plan selection
            if (! $coach->selected_plan) {
                return redirect()->route('coach.plan');
            }

            // Coach chose a plan but hasn't paid / trial expired — send to subscription page
            return redirect()->route('coach.subscription');
        }

        if ($isInGracePeriod) {
            $daysRemaining = $this->subscriptionService->graceDaysRemaining($coach);
            session()->flash('subscription_grace_days', $daysRemaining);
        }

        return $next($request);
    }
}
