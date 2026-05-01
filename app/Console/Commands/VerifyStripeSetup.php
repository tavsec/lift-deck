<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Throwable;

/**
 * Pre-flight check for Stripe configuration.
 *
 * Run this before flipping production keys live, after rotating any Stripe IDs,
 * or whenever subscription/billing behavior looks off in production. It does not
 * mutate anything in Stripe — read-only checks against env, prices, meter,
 * portal config, webhooks, and promo codes.
 *
 * Usage:
 *   php artisan stripe:verify
 *   php artisan stripe:verify --strict   (exit non-zero on warnings as well)
 */
class VerifyStripeSetup extends Command
{
    protected $signature = 'stripe:verify {--strict : Exit non-zero on warnings as well as failures}';

    protected $description = 'Verify the Stripe account is configured correctly for LiftDeck billing';

    private StripeClient $stripe;

    /** @var array<int, array{status: string, label: string, detail: string}> */
    private array $results = [];

    public function handle(): int
    {
        $secret = config('cashier.secret');

        if (! $secret) {
            $this->error('STRIPE_SECRET is not set.');

            return self::FAILURE;
        }

        $this->stripe = new StripeClient($secret);

        $this->line('');
        $this->line('  <fg=cyan;options=bold>Stripe configuration check</> '.($this->isLiveMode($secret) ? '<fg=red;options=bold>LIVE</>' : '<fg=yellow>test</>'));
        $this->line('');

        $this->checkApiKey();
        $this->checkPrices();
        $this->checkMeterEvent();
        $this->checkBillingPortal();
        $this->checkWebhookEndpoint();
        $this->checkPromotionCode('FOUNDING70');

        return $this->renderSummary();
    }

    private function checkApiKey(): void
    {
        try {
            $this->stripe->accounts->retrieve();
            $this->ok('API key works', 'Authenticated against Stripe.');
        } catch (Throwable $e) {
            $this->bad('API key works', $e->getMessage());
        }
    }

    private function checkPrices(): void
    {
        $expected = [
            'STRIPE_PRICE_BASIC' => [
                'env' => env('STRIPE_PRICE_BASIC'),
                'amount' => 1000,
                'currency' => 'eur',
                'usage_type' => 'licensed',
            ],
            'STRIPE_PRICE_ADVANCED' => [
                'env' => env('STRIPE_PRICE_ADVANCED'),
                'amount' => 4500,
                'currency' => 'eur',
                'usage_type' => 'licensed',
            ],
            'STRIPE_PRICE_PROFESSIONAL_FLAT' => [
                'env' => env('STRIPE_PRICE_PROFESSIONAL_FLAT'),
                'amount' => 7900,
                'currency' => 'eur',
                'usage_type' => 'licensed',
            ],
            'STRIPE_PRICE_PROFESSIONAL_METERED' => [
                'env' => env('STRIPE_PRICE_PROFESSIONAL_METERED'),
                'amount' => 50,
                'currency' => 'eur',
                'usage_type' => 'metered',
            ],
        ];

        foreach ($expected as $key => $exp) {
            $label = "Price {$key}";

            if (! $exp['env']) {
                $this->bad($label, 'env var is not set.');

                continue;
            }

            try {
                $price = $this->stripe->prices->retrieve($exp['env']);
            } catch (ApiErrorException $e) {
                $this->bad($label, "Price not found in Stripe: {$exp['env']}");

                continue;
            }

            if (! $price->active) {
                $this->bad($label, 'Price is inactive in Stripe.');

                continue;
            }

            if ($price->currency !== $exp['currency']) {
                $this->warning($label, "Currency is {$price->currency}, expected {$exp['currency']}.");

                continue;
            }

            $usageType = $price->recurring?->usage_type ?? null;
            if ($usageType !== $exp['usage_type']) {
                $this->bad($label, "usage_type is {$usageType}, expected {$exp['usage_type']}.");

                continue;
            }

            // Metered prices have unit_amount only as a unit cost reference; both can be checked the same way
            if ($price->unit_amount !== $exp['amount']) {
                $this->warning($label, "unit_amount is {$price->unit_amount}, expected {$exp['amount']}. Verify pricing changes.");

                continue;
            }

            $this->ok($label, "{$exp['env']} · {$price->currency} · ".number_format($price->unit_amount / 100, 2));
        }
    }

    private function checkMeterEvent(): void
    {
        $label = 'Meter event name';
        $appEvent = config('plans.professional.stripe_meter_event');
        $meteredPriceId = config('plans.professional.stripe_price_metered_id');

        if (! $appEvent) {
            $this->bad($label, 'STRIPE_METER_EVENT_PROFESSIONAL is not set; meter events will be silently dropped.');

            return;
        }

        if (! $meteredPriceId) {
            $this->bad($label, 'STRIPE_PRICE_PROFESSIONAL_METERED is not set; cannot resolve meter.');

            return;
        }

        try {
            $price = $this->stripe->prices->retrieve($meteredPriceId);
            $meterId = $price->recurring?->meter ?? null;

            if (! $meterId) {
                $this->bad($label, 'Metered price is not bound to a Stripe meter.');

                return;
            }

            $meter = $this->stripe->billing->meters->retrieve($meterId);
            if ($meter->event_name !== $appEvent) {
                $this->bad($label, "App sends '{$appEvent}' but Stripe meter expects '{$meter->event_name}'.");

                return;
            }

            if ($meter->status !== 'active') {
                $this->bad($label, "Meter status is '{$meter->status}', expected 'active'.");

                return;
            }

            $this->ok($label, "App and Stripe agree on '{$appEvent}'.");
        } catch (Throwable $e) {
            $this->bad($label, $e->getMessage());
        }
    }

    private function checkBillingPortal(): void
    {
        $label = 'Billing portal config';

        try {
            $configs = $this->stripe->billingPortal->configurations->all(['limit' => 5, 'is_default' => true]);
            if (count($configs->data) === 0) {
                $this->bad($label, 'No default billing portal configuration found.');

                return;
            }

            $cfg = $configs->data[0];
            $features = $cfg->features;

            $issues = [];

            if (! $features->subscription_update->enabled) {
                $issues[] = 'subscription_update is disabled (customers cannot upgrade/downgrade)';
            }
            if ($features->subscription_update->proration_behavior !== 'create_prorations') {
                $issues[] = "subscription_update.proration_behavior is '{$features->subscription_update->proration_behavior}', recommend 'create_prorations'";
            }
            if (! $features->subscription_cancel->enabled) {
                $issues[] = 'subscription_cancel is disabled';
            }
            if ($features->subscription_cancel->mode !== 'at_period_end') {
                $issues[] = "subscription_cancel.mode is '{$features->subscription_cancel->mode}', recommend 'at_period_end'";
            }
            if (! $features->payment_method_update->enabled) {
                $issues[] = 'payment_method_update is disabled';
            }
            if (! $features->invoice_history->enabled) {
                $issues[] = 'invoice_history is disabled';
            }

            // Verify the configured products list maps to our plans (or is null = all allowed)
            $products = $features->subscription_update->products ?? null;
            if (is_array($products) && count($products) > 0) {
                $portalPriceIds = collect($products)->flatMap(fn ($p) => $p->prices ?? [])->all();
                $expectedPriceIds = array_filter([
                    env('STRIPE_PRICE_BASIC'),
                    env('STRIPE_PRICE_ADVANCED'),
                    env('STRIPE_PRICE_PROFESSIONAL_FLAT'),
                ]);

                foreach ($expectedPriceIds as $expected) {
                    if (! in_array($expected, $portalPriceIds, true)) {
                        $issues[] = "portal allows switching but does not list price {$expected}";
                    }
                }
            }

            if ($issues) {
                $this->warning($label, implode('; ', $issues));

                return;
            }

            $this->ok($label, "Default config '{$cfg->name}' looks good.");
        } catch (Throwable $e) {
            $this->bad($label, $e->getMessage());
        }
    }

    private function checkWebhookEndpoint(): void
    {
        $label = 'Webhook endpoint';
        $appUrl = rtrim(config('app.url'), '/');
        $expectedUrl = $appUrl.'/stripe/webhook';

        $requiredEvents = [
            'customer.subscription.created',
            'customer.subscription.updated',
            'customer.subscription.deleted',
            'invoice.payment_succeeded',
            'invoice.payment_failed',
        ];

        try {
            $endpoints = $this->stripe->webhookEndpoints->all(['limit' => 100]);
            $matching = collect($endpoints->data)->filter(fn ($e) => str_contains($e->url, '/stripe/webhook'));

            if ($matching->isEmpty()) {
                $this->bad($label, "No webhook endpoint pointing at /stripe/webhook. Expected URL: {$expectedUrl}");

                return;
            }

            $exact = $matching->firstWhere('url', $expectedUrl);
            $endpoint = $exact ?: $matching->first();

            $issues = [];

            if (! $exact) {
                $issues[] = "no endpoint exactly matches {$expectedUrl} (found {$endpoint->url})";
            }

            if ($endpoint->status !== 'enabled') {
                $issues[] = "endpoint status is '{$endpoint->status}', expected 'enabled'";
            }

            $subscribedEvents = $endpoint->enabled_events ?? [];
            $listensToAll = in_array('*', $subscribedEvents, true);
            $missing = $listensToAll ? [] : array_diff($requiredEvents, $subscribedEvents);

            if ($missing) {
                $issues[] = 'endpoint is missing events: '.implode(', ', $missing);
            }

            // Webhook signing secret check: the endpoint's secret can only be retrieved once at creation
            // time, so we can only verify that one is set in env.
            if (! env('STRIPE_WEBHOOK_SECRET')) {
                $issues[] = 'STRIPE_WEBHOOK_SECRET is not set in env (webhook signature verification will fail)';
            }

            if ($issues) {
                $this->warning($label, implode('; ', $issues));

                return;
            }

            $this->ok($label, "{$endpoint->url} · enabled · ".(count($subscribedEvents) === 1 && $subscribedEvents[0] === '*' ? 'all events' : count($subscribedEvents).' events').'.');
        } catch (Throwable $e) {
            $this->bad($label, $e->getMessage());
        }
    }

    private function checkPromotionCode(string $code): void
    {
        $label = "Promo code {$code}";

        try {
            $codes = $this->stripe->promotionCodes->all(['code' => $code, 'limit' => 1, 'active' => true]);

            if (count($codes->data) === 0) {
                $this->warning($label, "Promo code '{$code}' (referenced in landing copy) is not active in Stripe. Either create it or remove the reference.");

                return;
            }

            $promo = $codes->data[0];
            $coupon = $promo->coupon;

            $detail = $coupon->percent_off
                ? "{$coupon->percent_off}% off"
                : ($coupon->amount_off ? number_format($coupon->amount_off / 100, 2).' '.strtoupper($coupon->currency).' off' : 'flat');

            $duration = $coupon->duration === 'repeating'
                ? "{$coupon->duration_in_months} month".($coupon->duration_in_months === 1 ? '' : 's')
                : $coupon->duration;

            $this->ok($label, "{$detail} · {$duration} · ".($promo->active ? 'active' : 'inactive'));
        } catch (Throwable $e) {
            $this->bad($label, $e->getMessage());
        }
    }

    private function ok(string $label, string $detail): void
    {
        $this->results[] = ['status' => 'pass', 'label' => $label, 'detail' => $detail];
        $this->line("  <fg=green>✓</> {$label}  <fg=gray>· {$detail}</>");
    }

    private function bad(string $label, string $detail): void
    {
        $this->results[] = ['status' => 'fail', 'label' => $label, 'detail' => $detail];
        $this->line("  <fg=red>✗</> {$label}  <fg=red>· {$detail}</>");
    }

    private function warning(string $label, string $detail): void
    {
        $this->results[] = ['status' => 'warn', 'label' => $label, 'detail' => $detail];
        $this->line("  <fg=yellow>!</> {$label}  <fg=yellow>· {$detail}</>");
    }

    private function isLiveMode(string $secret): bool
    {
        return str_starts_with($secret, 'sk_live_');
    }

    private function renderSummary(): int
    {
        $pass = count(array_filter($this->results, fn ($r) => $r['status'] === 'pass'));
        $warn = count(array_filter($this->results, fn ($r) => $r['status'] === 'warn'));
        $fail = count(array_filter($this->results, fn ($r) => $r['status'] === 'fail'));

        $this->line('');
        $this->line("  Summary: <fg=green>{$pass} passed</>, <fg=yellow>{$warn} warnings</>, <fg=red>{$fail} failed</>");
        $this->line('');

        if ($fail > 0) {
            return self::FAILURE;
        }

        if ($warn > 0 && $this->option('strict')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
