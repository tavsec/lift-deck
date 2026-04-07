<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Laravel\Cashier\Cashier;

class StripePriceService
{
    /**
     * Returns formatted price data for all configured plans, cached for 24 hours.
     * Falls back to an empty array if the Stripe API is unavailable.
     *
     * @return array<string, array{formatted: string, metered_formatted?: string}>
     */
    public function forPlans(): array
    {
        try {
            return Cache::remember('stripe_plan_prices', now()->addDay(), function (): array {
                $result = [];

                foreach (config('plans') as $key => $plan) {
                    $priceId = $plan['stripe_price_id'] ?? $plan['stripe_price_flat_id'] ?? null;

                    if (! $priceId) {
                        continue;
                    }

                    $price = Cashier::stripe()->prices->retrieve($priceId);
                    $result[$key] = ['formatted' => $this->format($price->unit_amount / 100)];

                    if (! empty($plan['stripe_price_metered_id'])) {
                        $metered = Cashier::stripe()->prices->retrieve($plan['stripe_price_metered_id']);

                        if ($metered->unit_amount !== null) {
                            $result[$key]['metered_formatted'] = $this->format($metered->unit_amount / 100);
                        }
                    }
                }

                return $result;
            });
        } catch (\Exception) {
            return [];
        }
    }

    private function format(float $amount): string
    {
        return rtrim(rtrim(number_format($amount, 2), '0'), '.');
    }
}
