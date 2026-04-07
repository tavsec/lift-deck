<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanSelectionRequest;
use App\Services\StripePriceService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanSelectionController extends Controller
{
    public function __construct(
        private readonly SubscriptionService $subscriptionService,
        private readonly StripePriceService $stripePriceService,
    ) {}

    public function show(): View|RedirectResponse
    {
        $coach = auth()->user();

        // Free access and coaches with an active Stripe subscription don't need to select a plan
        if ($coach->is_free_access || $coach->subscribed('default')) {
            return redirect()->route('coach.dashboard');
        }

        // Coach abandoned checkout (selected a plan, not on trial, no subscription yet)
        if ($coach->selected_plan && ! $coach->onTrial()) {
            return redirect()->route('coach.subscription');
        }

        return view('coach.plan', [
            'plans' => config('plans'),
            'stripePrices' => $this->stripePriceService->forPlans(),
            'currentPlanKey' => $this->subscriptionService->currentPlanKey($coach),
        ]);
    }

    public function store(StorePlanSelectionRequest $request): RedirectResponse
    {
        $coach = auth()->user();

        $coach->update(['selected_plan' => $request->validated('plan')]);

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
