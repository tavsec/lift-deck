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
        $trialEndsAt = $coach->trial_ends_at ?? $coach->subscription('default')?->trial_ends_at;
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
     * All three plans go through Stripe Checkout. Basic includes a 7-day trial
     * configured via config('plans.basic.trial_days').
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

        if ($coach->is_free_access || $coach->subscribed('default')) {
            return redirect()->route('coach.dashboard');
        }

        if ($coach->trial_ends_at?->isFuture()) {
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
                ->allowPromotionCodes()
                ->checkout($checkoutOptions)
                ->toResponse(request());
        }

        $builder = $coach->newSubscription('default', $plan['stripe_price_id']);

        if (($plan['trial_days'] ?? 0) > 0) {
            $builder->trialDays($plan['trial_days']);

            // No-credit-card trial: don't ask for payment method up-front.
            // If the customer hasn't added one by trial end, Stripe cancels
            // the subscription automatically.
            $checkoutOptions['payment_method_collection'] = 'if_required';
            $checkoutOptions['subscription_data'] = [
                'trial_settings' => [
                    'end_behavior' => [
                        'missing_payment_method' => 'cancel',
                    ],
                ],
            ];
        }

        return $builder->allowPromotionCodes()->checkout($checkoutOptions)->toResponse(request());
    }
}
