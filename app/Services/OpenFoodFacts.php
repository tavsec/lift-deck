<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class OpenFoodFacts
{
    private const BASE = 'https://world.openfoodfacts.org';

    private const TIMEOUT = 5;

    private const USER_AGENT = 'LiftDeck/1.0 (https://liftdeck.io)';

    private const SEARCH_TTL = 86400;

    private const PRODUCT_TTL = 604800;

    private const MAX_QUERY_LENGTH = 80;

    private const MIN_QUERY_LENGTH = 2;

    /**
     * Search products by name.
     *
     * @return Collection<int, array{code: string, name: string, brand: ?string, image: ?string, kcal_per_100g: float, protein_per_100g: float, carbs_per_100g: float, fat_per_100g: float}>
     */
    public function search(string $query): Collection
    {
        $normalized = mb_substr(trim($query), 0, self::MAX_QUERY_LENGTH);

        if (mb_strlen($normalized) < self::MIN_QUERY_LENGTH) {
            return collect();
        }

        $cacheKey = 'off:search:'.sha1(mb_strtolower($normalized));

        return Cache::remember($cacheKey, self::SEARCH_TTL, function () use ($normalized): Collection {
            try {
                $response = Http::withUserAgent(self::USER_AGENT)
                    ->timeout(self::TIMEOUT)
                    ->get(self::BASE.'/cgi/search.pl', [
                        'search_terms' => $normalized,
                        'search_simple' => 1,
                        'action' => 'process',
                        'json' => 1,
                        'page_size' => 20,
                        'fields' => 'code,product_name,brands,nutriments,image_thumb_url',
                    ]);

                if (! $this->isValidResponse($response)) {
                    return collect();
                }

                $payload = $response->json();
                $products = data_get($payload, 'products', []);

                if (! is_array($products)) {
                    return collect();
                }

                return collect($products)
                    ->map(fn ($product): ?array => $this->normalizeProduct($product))
                    ->filter()
                    ->values();
            } catch (Throwable) {
                return collect();
            }
        });
    }

    /**
     * Look up a single product by barcode.
     *
     * @return array{code: string, name: string, brand: ?string, image: ?string, kcal_per_100g: float, protein_per_100g: float, carbs_per_100g: float, fat_per_100g: float}|null
     */
    public function findByBarcode(string $barcode): ?array
    {
        $code = trim($barcode);

        if ($code === '' || ! preg_match('/^[0-9]{4,20}$/', $code)) {
            return null;
        }

        $cacheKey = 'off:product:'.$code;

        return Cache::remember($cacheKey, self::PRODUCT_TTL, function () use ($code): ?array {
            try {
                $response = Http::withUserAgent(self::USER_AGENT)
                    ->timeout(self::TIMEOUT)
                    ->get(self::BASE.'/api/v2/product/'.$code.'.json', [
                        'fields' => 'code,product_name,brands,nutriments,image_thumb_url',
                    ]);

                if (! $this->isValidResponse($response)) {
                    return null;
                }

                $payload = $response->json();
                $status = data_get($payload, 'status');

                if ($status !== 1 && $status !== '1') {
                    return null;
                }

                $product = data_get($payload, 'product');

                if (! is_array($product)) {
                    return null;
                }

                return $this->normalizeProduct($product);
            } catch (Throwable) {
                return null;
            }
        });
    }

    private function isValidResponse(Response $response): bool
    {
        if (! $response->successful()) {
            return false;
        }

        try {
            $decoded = $response->json();
        } catch (Throwable) {
            return false;
        }

        return is_array($decoded);
    }

    /**
     * @return array{code: string, name: string, brand: ?string, image: ?string, kcal_per_100g: float, protein_per_100g: float, carbs_per_100g: float, fat_per_100g: float}|null
     */
    private function normalizeProduct(mixed $product): ?array
    {
        if (! is_array($product)) {
            return null;
        }

        $kcal = data_get($product, 'nutriments.energy-kcal_100g');

        if (! is_numeric($kcal)) {
            return null;
        }

        $name = trim((string) data_get($product, 'product_name', ''));

        if ($name === '') {
            return null;
        }

        $code = (string) data_get($product, 'code', '');

        if ($code === '') {
            return null;
        }

        $brand = data_get($product, 'brands');
        $brandValue = is_string($brand) && trim($brand) !== '' ? trim(explode(',', $brand)[0]) : null;

        $image = data_get($product, 'image_thumb_url');
        $imageValue = is_string($image) && $image !== '' ? $image : null;

        return [
            'code' => $code,
            'name' => $name,
            'brand' => $brandValue,
            'image' => $imageValue,
            'kcal_per_100g' => round((float) $kcal, 1),
            'protein_per_100g' => round((float) (data_get($product, 'nutriments.proteins_100g') ?? 0), 2),
            'carbs_per_100g' => round((float) (data_get($product, 'nutriments.carbohydrates_100g') ?? 0), 2),
            'fat_per_100g' => round((float) (data_get($product, 'nutriments.fat_100g') ?? 0), 2),
        ];
    }
}
