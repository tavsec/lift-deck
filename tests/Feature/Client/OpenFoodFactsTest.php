<?php

use App\Models\User;
use App\Services\OpenFoodFacts;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
});

function offSearchPayload(): array
{
    return [
        'count' => 1,
        'page' => 1,
        'page_size' => 20,
        'products' => [
            [
                'code' => '3017620422003',
                'product_name' => 'Nutella',
                'brands' => 'Ferrero',
                'image_thumb_url' => 'https://images.openfoodfacts.org/images/products/301/762/042/2003/front.100.jpg',
                'nutriments' => [
                    'energy-kcal_100g' => 539,
                    'proteins_100g' => 6.3,
                    'carbohydrates_100g' => 57.5,
                    'fat_100g' => 30.9,
                ],
            ],
            [
                'code' => '0000000000000',
                'product_name' => 'No Macros Product',
                'brands' => 'Acme',
                'nutriments' => [],
            ],
        ],
    ];
}

it('returns normalized search results from OFF JSON', function () {
    Http::fake([
        'world.openfoodfacts.org/*' => Http::response(offSearchPayload(), 200),
    ]);

    $results = (new OpenFoodFacts)->search('nutella');

    expect($results)->toHaveCount(1);

    $first = $results->first();
    expect($first)
        ->toHaveKeys(['code', 'name', 'brand', 'image', 'kcal_per_100g', 'protein_per_100g', 'carbs_per_100g', 'fat_per_100g'])
        ->and($first['code'])->toBe('3017620422003')
        ->and($first['name'])->toBe('Nutella')
        ->and($first['brand'])->toBe('Ferrero')
        ->and($first['kcal_per_100g'])->toBe(539.0)
        ->and($first['protein_per_100g'])->toBe(6.3)
        ->and($first['carbs_per_100g'])->toBe(57.5)
        ->and($first['fat_per_100g'])->toBe(30.9);
});

it('returns empty collection on HTTP 500 response', function () {
    Http::fake([
        'world.openfoodfacts.org/*' => Http::response('Server error', 500),
    ]);

    $results = (new OpenFoodFacts)->search('nutella');

    expect($results)->toBeEmpty();
});

it('returns empty collection when JSON is malformed', function () {
    Http::fake([
        'world.openfoodfacts.org/*' => Http::response('not-json-at-all', 200),
    ]);

    $results = (new OpenFoodFacts)->search('nutella');

    expect($results)->toBeEmpty();
});

it('caches search results between calls', function () {
    Http::fake([
        'world.openfoodfacts.org/*' => Http::response(offSearchPayload(), 200),
    ]);

    $service = new OpenFoodFacts;
    $first = $service->search('nutella');
    $second = $service->search('nutella');

    expect($first)->toHaveCount(1)
        ->and($second)->toHaveCount(1);

    Http::assertSentCount(1);
});

it('returns empty collection without an HTTP call when query is too short', function () {
    Http::fake();

    $results = (new OpenFoodFacts)->search('a');

    expect($results)->toBeEmpty();
    Http::assertNothingSent();
});

it('filters out products without energy-kcal_100g', function () {
    Http::fake([
        'world.openfoodfacts.org/*' => Http::response(offSearchPayload(), 200),
    ]);

    $results = (new OpenFoodFacts)->search('nutella');

    expect($results->pluck('code')->all())->toBe(['3017620422003']);
});

describe('food-search endpoint', function () {
    beforeEach(function () {
        $this->coach = User::factory()->create(['role' => 'coach']);
        $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    });

    it('returns JSON results for an authenticated client', function () {
        Http::fake([
            'world.openfoodfacts.org/*' => Http::response(offSearchPayload(), 200),
        ]);

        $this->actingAs($this->client)
            ->getJson(route('client.nutrition.food-search', ['q' => 'nutella']))
            ->assertOk()
            ->assertJsonStructure([
                'results' => [
                    ['code', 'name', 'brand', 'image', 'kcal_per_100g', 'protein_per_100g', 'carbs_per_100g', 'fat_per_100g'],
                ],
            ])
            ->assertJsonPath('results.0.code', '3017620422003');
    });

    it('redirects unauthenticated users to login', function () {
        $this->get(route('client.nutrition.food-search', ['q' => 'nutella']))
            ->assertRedirect(route('login'));
    });

    it('blocks coaches from the client food-search endpoint', function () {
        $this->actingAs($this->coach)
            ->get(route('client.nutrition.food-search', ['q' => 'nutella']))
            ->assertRedirect(route('coach.dashboard'));
    });
});
