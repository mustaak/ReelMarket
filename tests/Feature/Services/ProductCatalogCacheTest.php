<?php

namespace Tests\Feature\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\ProductCatalogCache;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductCatalogCacheTest extends TestCase
{
    use DatabaseMigrations;

    public function test_cached_product_data_is_reused_and_invalidated_after_catalog_changes(): void
    {
        config(['cache.default' => 'database']);
        Cache::flush();

        $product = Product::factory()->create(['name' => 'Original product']);
        $productCatalogCache = app(ProductCatalogCache::class);
        $loadCount = 0;
        $loadProductData = function () use ($product, &$loadCount): array {
            $loadCount++;

            $freshProduct = $product->fresh();

            return [
                'name' => $freshProduct->name,
                'images' => $freshProduct->images->pluck('image')->all(),
            ];
        };

        self::assertSame(
            ['name' => 'Original product', 'images' => []],
            $productCatalogCache->remember('test-product', $loadProductData),
        );
        $productCatalogCache->remember('test-product', $loadProductData);
        self::assertSame(1, $loadCount);

        $product->update(['name' => 'Updated product']);

        self::assertSame(
            ['name' => 'Updated product', 'images' => []],
            $productCatalogCache->remember('test-product', $loadProductData),
        );

        ProductImage::create([
            'product_id' => $product->id,
            'image' => 'updated-product.jpg',
            'sort_order' => 0,
        ]);

        self::assertSame(
            ['name' => 'Updated product', 'images' => ['updated-product.jpg']],
            $productCatalogCache->remember('test-product', $loadProductData),
        );
        self::assertSame(3, $loadCount);
    }

    public function test_negative_product_lookups_are_cached(): void
    {
        config(['cache.default' => 'database']);
        Cache::flush();

        $productCatalogCache = app(ProductCatalogCache::class);
        $loadCount = 0;
        $loadMissingProduct = function () use (&$loadCount): array {
            $loadCount++;

            return ['id' => null];
        };

        self::assertSame(['id' => null], $productCatalogCache->remember('missing-deal', $loadMissingProduct));
        self::assertSame(['id' => null], $productCatalogCache->remember('missing-deal', $loadMissingProduct));
        self::assertSame(1, $loadCount);
    }

    public function test_variant_and_attribute_changes_invalidate_cached_product_data(): void
    {
        config(['cache.default' => 'database']);
        Cache::flush();

        $product = Product::factory()->create();
        $attribute = Attribute::create([
            'name' => 'Color',
            'slug' => 'color',
        ]);
        $attributeValue = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Blue',
        ]);
        $productCatalogCache = app(ProductCatalogCache::class);
        $loadCount = 0;
        $loadProductRelations = function () use ($product, &$loadCount): array {
            $loadCount++;

            $freshProduct = $product->fresh();

            return [
                'variants' => $freshProduct->variants->pluck('stock_quantity')->all(),
                'attributes' => $freshProduct->attributeValues->pluck('id')->all(),
            ];
        };

        self::assertSame(
            ['variants' => [], 'attributes' => []],
            $productCatalogCache->remember('product-relations', $loadProductRelations),
        );

        ProductVariant::factory()->for($product)->create(['stock_quantity' => 10]);

        self::assertSame(
            ['variants' => [10], 'attributes' => []],
            $productCatalogCache->remember('product-relations', $loadProductRelations),
        );

        $product->attributeValues()->attach($attributeValue->id);

        self::assertSame(
            ['variants' => [10], 'attributes' => [$attributeValue->id]],
            $productCatalogCache->remember('product-relations', $loadProductRelations),
        );
        self::assertSame(3, $loadCount);
    }
}
