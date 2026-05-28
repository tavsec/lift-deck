<?php

namespace App\Services;

use App\Models\User;

class SubscriptionService
{
    /**
     * Whether the coach has an active subscription, active trial, or free access.
     */
    public function isActive(User $coach): bool
    {
        if ($coach->is_free_access) {
            return true;
        }

        if ($coach->subscribed('default')) {
            return true;
        }

        if ($coach->onTrial()) {
            return true;
        }

        return false;
    }

    /**
     * Whether the coach is within the 7-day grace period after subscription ended.
     */
    public function isInGracePeriod(User $coach): bool
    {
        if ($coach->is_free_access) {
            return false;
        }

        return (bool) $coach->subscription('default')?->onGracePeriod();
    }

    /**
     * Returns the days remaining in the grace period, or 0.
     */
    public function graceDaysRemaining(User $coach): int
    {
        if (! $this->isInGracePeriod($coach)) {
            return 0;
        }

        $subscription = $coach->subscription('default');
        if (! $subscription?->ends_at) {
            return 0;
        }

        return (int) max(0, now()->diffInDays($subscription->ends_at, false));
    }

    /**
     * Returns the active plan config array, or null if no active subscription.
     *
     * @return array<string, mixed>|null
     */
    public function currentPlan(User $coach): ?array
    {
        if ($coach->is_free_access) {
            return config('plans.professional');
        }

        $subscription = $coach->subscription('default');
        if (! $subscription) {
            if ($coach->trial_ends_at?->isFuture() && $coach->selected_plan) {
                return config("plans.{$coach->selected_plan}");
            }

            return null;
        }

        $planKey = $this->currentPlanKey($coach);

        return $planKey ? config("plans.{$planKey}") : null;
    }

    /**
     * Returns the plan key (e.g. 'basic', 'advanced', 'professional'), or null.
     */
    public function currentPlanKey(User $coach): ?string
    {
        if ($coach->is_free_access) {
            return 'professional';
        }

        $subscription = $coach->subscription('default');
        if (! $subscription) {
            if ($coach->trial_ends_at?->isFuture() && $coach->selected_plan) {
                return $coach->selected_plan;
            }

            return null;
        }

        // Single-price subscription
        if ($subscription->stripe_price) {
            return $this->findPlanKeyByPriceId($subscription->stripe_price);
        }

        // Multi-price subscription (e.g. professional flat + metered) — check items
        foreach ($subscription->items as $item) {
            $key = $this->findPlanKeyByPriceId($item->stripe_price);
            if ($key) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Finds the plan key for a given Stripe price ID, or null if not found.
     */
    private function findPlanKeyByPriceId(string $priceId): ?string
    {
        foreach (config('plans') as $key => $plan) {
            if (isset($plan['stripe_price_id']) && $plan['stripe_price_id'] === $priceId) {
                return $key;
            }
            if (isset($plan['stripe_price_flat_id']) && $plan['stripe_price_flat_id'] === $priceId) {
                return $key;
            }
        }

        return null;
    }

    /**
     * The maximum number of clients the coach can have, or null for unlimited.
     */
    public function clientLimit(User $coach): ?int
    {
        $plan = $this->currentPlan($coach);

        return $plan['client_limit'] ?? null;
    }

    /**
     * Returns the number of clients beyond the included threshold for metered plans, or null for non-metered plans.
     */
    public function meteredClientCount(User $coach, ?int $clientCount = null): ?int
    {
        if ($this->currentPlanKey($coach) !== 'professional') {
            return null;
        }

        $included = config('plans.professional.included_clients', 30);
        $count = $clientCount ?? $coach->clients()->count();

        return max(0, $count - $included);
    }

    /**
     * Whether the coach can add another client.
     */
    public function canAddClient(User $coach): bool
    {
        $limit = $this->clientLimit($coach);

        if ($limit === null) {
            return true;
        }

        return $coach->clients()->count() < $limit;
    }

    /**
     * Whether the coach's plan includes the given feature.
     * Features: 'loyalty', 'custom_branding'
     */
    public function hasFeature(User $coach, string $feature): bool
    {
        if ($coach->is_free_access) {
            return true;
        }

        $plan = $this->currentPlan($coach);

        if (! $plan) {
            return false;
        }

        return in_array($feature, $plan['features'] ?? [], true);
    }

    /**
     * Reports current overage client count to Stripe for Professional metered billing.
     * Should be called after any client add/remove for Professional plan coaches.
     */
    public function reportClientUsage(User $coach): void
    {
        if ($coach->is_free_access) {
            return;
        }

        if ($this->currentPlanKey($coach) !== 'professional') {
            return;
        }

        if (! $coach->subscription('default')) {
            return;
        }

        $includedClients = config('plans.professional.included_clients', 30);
        $totalClients = $coach->clients()->count();
        $overageClients = max(0, $totalClients - $includedClients);

        $meterEvent = config('plans.professional.stripe_meter_event', 'clients');

        $coach->reportMeterEvent($meterEvent, quantity: $overageClients);
    }
}
