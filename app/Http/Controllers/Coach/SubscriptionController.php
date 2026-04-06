<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptionService) {}

    public function index(): View
    {
        $coach = auth()->user();
        $currentPlanKey = $this->subscriptionService->currentPlanKey($coach);
        $clientCount = $coach->clients()->count();
        $clientLimit = $this->subscriptionService->clientLimit($coach);
        $isOnTrial = $coach->onTrial();
        $trialEndsAt = $coach->trial_ends_at;
        $isInGracePeriod = $this->subscriptionService->isInGracePeriod($coach);
        $graceDaysRemaining = $this->subscriptionService->graceDaysRemaining($coach);
        $plans = config('plans');
        $subscription = $coach->subscription('default');
        $selectedPlan = $coach->selected_plan;

        return view('coach.subscription', compact(
            'currentPlanKey',
            'clientCount',
            'clientLimit',
            'isOnTrial',
            'trialEndsAt',
            'isInGracePeriod',
            'graceDaysRemaining',
            'plans',
            'subscription',
            'selectedPlan',
        ));
    }

    /**
     * Redirects the coach to the Stripe Customer Portal.
     *
     * The Stripe Customer Portal must be configured in the Stripe Dashboard to support
     * subscription management (selecting and subscribing to new plans).
     */
    public function portal(): RedirectResponse
    {
        $coach = auth()->user();

        if (! $coach->stripe_id) {
            $coach->createAsStripeCustomer();
        }

        return $coach->redirectToBillingPortal(route('coach.subscription'));
    }

    /**
     * Redirects the coach to Stripe Checkout for their selected plan.
     * Used both when a new coach picks Advanced/Professional and when a
     * Basic trial coach needs to subscribe after the trial ends.
     *
     * Stripe Checkout session creation makes a live API call — test the guard
     * conditions only (no selected_plan, already subscribed).
     */
    public function checkout(): Response
    {
        $coach = auth()->user();

        if (! $coach->selected_plan) {
            return redirect()->route('coach.plan');
        }

        if ($this->subscriptionService->isActive($coach)) {
            return redirect()->route('coach.dashboard');
        }

        $plan = config("plans.{$coach->selected_plan}");

        abort_if($plan === null, 404);

        $checkoutOptions = [
            'success_url' => route('coach.plan.success'),
            'cancel_url' => route('coach.subscription'),
        ];

        if ($coach->selected_plan === 'professional') {
            return $coach->newSubscription('default', $plan['stripe_price_flat_id'])
                ->meteredPrice($plan['stripe_price_metered_id'])
                ->checkout($checkoutOptions);
        }

        return $coach->newSubscription('default', $plan['stripe_price_id'])
            ->checkout($checkoutOptions);
    }
}
