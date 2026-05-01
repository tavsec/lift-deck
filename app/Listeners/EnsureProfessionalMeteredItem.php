<?php

namespace App\Listeners;

use App\Models\User;
use Laravel\Cashier\Events\WebhookHandled;

/**
 * When a subscription is updated to the Professional flat price (e.g. via Stripe
 * Customer Portal upgrade), the portal cannot add the metered overage item, so
 * we attach it server-side. Without this, coaches with >30 clients on the
 * Professional plan would not be billed for overage.
 */
class EnsureProfessionalMeteredItem
{
    public function handle(WebhookHandled $event): void
    {
        $type = $event->payload['type'] ?? null;

        if (! in_array($type, ['customer.subscription.updated', 'customer.subscription.created'], true)) {
            return;
        }

        $subscriptionData = $event->payload['data']['object'] ?? [];
        $stripeCustomerId = $subscriptionData['customer'] ?? null;
        $items = $subscriptionData['items']['data'] ?? [];

        if (! $stripeCustomerId || empty($items)) {
            return;
        }

        $proFlatPriceId = config('plans.professional.stripe_price_flat_id');
        $proMeteredPriceId = config('plans.professional.stripe_price_metered_id');

        if (! $proFlatPriceId || ! $proMeteredPriceId) {
            return;
        }

        $itemPriceIds = array_map(
            fn (array $item): ?string => $item['price']['id'] ?? null,
            $items
        );

        $hasFlat = in_array($proFlatPriceId, $itemPriceIds, true);
        $hasMetered = in_array($proMeteredPriceId, $itemPriceIds, true);

        if (! $hasFlat || $hasMetered) {
            return;
        }

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if (! $user) {
            return;
        }

        $subscription = $user->subscription('default');

        if (! $subscription) {
            return;
        }

        $subscription->addMeteredPrice($proMeteredPriceId);
    }
}
