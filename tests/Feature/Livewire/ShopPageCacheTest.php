<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ShopPage;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class ShopPageCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_search_uses_distinct_cached_product_results(): void
    {
        config(['cache.default' => 'database']);
        Cache::flush();

        Product::factory()->create(['name' => 'Alpha product']);
        Product::factory()->create(['name' => 'Beta product']);

        for ($index = 1; $index <= 10; $index++) {
            Product::factory()->create([
                'name' => "Catalog item {$index}",
                'created_at' => now()->subHours($index),
            ]);
        }

        Livewire::test(ShopPage::class)
            ->set('search', 'Alpha product')
            ->assertSee('Alpha product')
            ->assertDontSee('Beta product')
            ->set('search', 'Beta product')
            ->assertSee('Beta product')
            ->assertDontSee('Alpha product');

        Livewire::test(ShopPage::class)
            ->set('search', 'Beta product')
            ->assertSee('Beta product')
            ->assertDontSee('Alpha product');

        Livewire::test(ShopPage::class)
            ->call('setPage', 2)
            ->assertSee('Catalog item 8')
            ->assertDontSee('Catalog item 7');
    }
}
