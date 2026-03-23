<?php

return [
    'basic' => [
        'stripe_price_id' => env('STRIPE_PRICE_BASIC'),
        'client_limit' => 5,
        'features' => [],
        'trial_days' => 7,
    ],
    'advanced' => [
        'stripe_price_id' => env('STRIPE_PRICE_ADVANCED'),
        'client_limit' => 15,
        'features' => ['loyalty'],
        'trial_days' => 0,
    ],
    'professional' => [
        'stripe_price_flat_id' => env('STRIPE_PRICE_PROFESSIONAL_FLAT'),
        'stripe_price_metered_id' => env('STRIPE_PRICE_PROFESSIONAL_METERED'),
        'client_limit' => null,
        'included_clients' => 30,
        'features' => ['loyalty', 'custom_branding'],
        'trial_days' => 0,
    ],
];
