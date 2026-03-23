<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        ));
    }

    public function portal(): RedirectResponse
    {
        $coach = auth()->user();

        if (! $coach->stripe_id) {
            $coach->createAsStripeCustomer();
        }

        return $coach->redirectToBillingPortal(route('coach.subscription'));
    }
}
