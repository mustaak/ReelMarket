<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

class ProductCatalogCache
{
    private const GENERATION_KEY = 'product-catalog:v2:generation';

    private const CACHE_TTL_MINUTES = 10;

    private const CACHE_KEY_PREFIX = 'product-catalog:v2:';

    /**
     * @param  Closure(): array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    public function remember(string $key, Closure $callback): array
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX.$this->generation().':'.$key,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn (): array => $callback(),
        );
    }

    public function invalidate(): void
    {
        Cache::add(self::GENERATION_KEY, 1);
        Cache::increment(self::GENERATION_KEY);
    }

    private function generation(): int
    {
        Cache::add(self::GENERATION_KEY, 1);

        return (int) Cache::get(self::GENERATION_KEY, 1);
    }
}
