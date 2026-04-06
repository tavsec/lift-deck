<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanSelectionRequest;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanSelectionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function show(): View|RedirectResponse
    {
        $coach = auth()->user();

        if ($this->subscriptionService->isActive($coach)) {
            return redirect()->route('coach.dashboard');
        }

        if ($coach->selected_plan) {
            return redirect()->route('coach.subscription');
        }

        return view('coach.plan', [
            'plans' => config('plans'),
        ]);
    }

    public function store(StorePlanSelectionRequest $request): RedirectResponse
    {
        $coach = auth()->user();
        $plan = $request->validated('plan');

        if ($plan === 'basic') {
            $coach->update([
                'selected_plan' => $plan,
                'trial_ends_at' => now()->addDays(config('plans.basic.trial_days', 7)),
            ]);

            return redirect()->route('coach.dashboard');
        }

        $coach->update(['selected_plan' => $plan]);

        return redirect()->route('coach.subscription.checkout');
    }

    /**
     * Handles the Stripe Checkout return URL after a successful payment.
     * Subscription activation is handled asynchronously by Cashier's webhook handler.
     */
    public function success(): RedirectResponse
    {
        return redirect()->route('coach.dashboard');
    }
}
