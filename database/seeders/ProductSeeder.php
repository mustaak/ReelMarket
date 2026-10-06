<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            /*
            |--------------------------------------------------------------------------
            | 1. Simple Product
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Wireless Bluetooth Headphones',
                'category' => 'Mobiles',
                'brand' => 'Sony',
                'price' => 4999,
                'sale_price' => 3999,
                'stock' => 45,
                'attributes' => ['Black', 'White'],
                'variants' => [],
                'images' => [
                    'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=80',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 2. Product With Variants
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Running Shoes',
                'category' => null,
                'brand' => 'Nike',
                'price' => 5499,
                'sale_price' => null,

                // Fallback product stock.
                // Actual stock is managed through variants.
                'stock' => 30,

                'attributes' => [
                    'Red',
                    'Black',
                    'M',
                    'L',
                    'XL',
                ],

                'variants' => [
                    [
                        'label' => 'Red / M',
                        'sku' => 'SHOE-RED-M',
                        'options' => [
                            'Color' => 'Red',
                            'Size' => 'M',
                        ],
                        'price' => 5499,
                        'stock_quantity' => 10,
                    ],
                    [
                        'label' => 'Red / L',
                        'sku' => 'SHOE-RED-L',
                        'options' => [
                            'Color' => 'Red',
                            'Size' => 'L',
                        ],
                        'price' => 5499,
                        'stock_quantity' => 5,
                    ],
                    [
                        'label' => 'Red / XL',
                        'sku' => 'SHOE-RED-XL',
                        'options' => [
                            'Color' => 'Red',
                            'Size' => 'XL',
                        ],
                        'price' => 5499,
                        'stock_quantity' => 3,
                    ],
                    [
                        'label' => 'Black / M',
                        'sku' => 'SHOE-BLACK-M',
                        'options' => [
                            'Color' => 'Black',
                            'Size' => 'M',
                        ],
                        'price' => 5499,
                        'stock_quantity' => 8,
                    ],
                    [
                        'label' => 'Black / L',
                        'sku' => 'SHOE-BLACK-L',
                        'options' => [
                            'Color' => 'Black',
                            'Size' => 'L',
                        ],
                        'price' => 5499,
                        'stock_quantity' => 0,
                    ],
                ],

                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?auto=format&fit=crop&w=1200&q=80',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 3. Simple Product
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Smart LED TV 43 inch',
                'category' => 'Electronics',
                'brand' => 'Samsung',
                'price' => 32999,
                'sale_price' => 28999,
                'stock' => 12,
                'attributes' => [],
                'variants' => [],
                'images' => [
                    'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1490933991293-4e1d37c4d1ce?auto=format&fit=crop&w=1200&q=80',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 4. Product With Variants
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Cotton Casual T-Shirt',
                'category' => 'Men',
                'brand' => 'Puma',
                'price' => 899,
                'sale_price' => 649,

                // Variant product.
                'stock' => 0,

                'attributes' => [
                    'Blue',
                    'White',
                    'S',
                    'M',
                    'L',
                    'Cotton',
                ],

                'variants' => [
                    [
                        'label' => 'Blue / S',
                        'sku' => 'TSHIRT-BLUE-S',
                        'options' => [
                            'Color' => 'Blue',
                            'Size' => 'S',
                        ],
                        'price' => 649,
                        'stock_quantity' => 20,
                    ],
                    [
                        'label' => 'Blue / M',
                        'sku' => 'TSHIRT-BLUE-M',
                        'options' => [
                            'Color' => 'Blue',
                            'Size' => 'M',
                        ],
                        'price' => 649,
                        'stock_quantity' => 25,
                    ],
                    [
                        'label' => 'Blue / L',
                        'sku' => 'TSHIRT-BLUE-L',
                        'options' => [
                            'Color' => 'Blue',
                            'Size' => 'L',
                        ],
                        'price' => 649,
                        'stock_quantity' => 15,
                    ],
                    [
                        'label' => 'White / S',
                        'sku' => 'TSHIRT-WHITE-S',
                        'options' => [
                            'Color' => 'White',
                            'Size' => 'S',
                        ],
                        'price' => 649,
                        'stock_quantity' => 10,
                    ],
                    [
                        'label' => 'White / M',
                        'sku' => 'TSHIRT-WHITE-M',
                        'options' => [
                            'Color' => 'White',
                            'Size' => 'M',
                        ],
                        'price' => 649,
                        'stock_quantity' => 18,
                    ],
                    [
                        'label' => 'White / L',
                        'sku' => 'TSHIRT-WHITE-L',
                        'options' => [
                            'Color' => 'White',
                            'Size' => 'L',
                        ],
                        'price' => 649,
                        'stock_quantity' => 0,
                    ],
                ],

                'images' => [
                    'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1200&q=80',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 5. Simple Product
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Gaming Laptop 15.6"',
                'category' => 'Laptops',
                'brand' => 'Apple',
                'price' => 89999,
                'sale_price' => null,
                'stock' => 5,
                'attributes' => [],
                'variants' => [],
                'images' => [
                    'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=80',
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | 6. Out Of Stock Simple Product
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Yoga Mat',
                'category' => 'Fitness Equipment',
                'brand' => null,
                'price' => 1299,
                'sale_price' => 999,
                'stock' => 0,
                'attributes' => ['Green'],
                'variants' => [],
                'images' => [
                    'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
        ];

        Storage::disk('public')->makeDirectory('products');

        foreach ($products as $index => $data) {
            $slug = Str::slug($data['name']);

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */
            $product = Product::updateOrCreate(
                [
                    'slug' => $slug,
                ],
                [
                    'name' => $data['name'],
                    'sku' => $this->productSku($data['name']),
                    'short_description' => "High quality {$data['name']}.",
                    'description' => "<p>This is a detailed description for {$data['name']}. Great quality and value for money.</p>",
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'cost_price' => round($data['price'] * 0.6, 2),
                    'stock_quantity' => $data['stock'],
                    'status' => 'active',
                    'visibility' => 'visible',
                    'featured' => $index < 2,
                    'category_id' => $data['category']
                        ? Category::where('name', $data['category'])->value('id')
                        : null,
                    'brand_id' => $data['brand']
                        ? Brand::where('name', $data['brand'])->value('id')
                        : null,
                    'weight' => rand(1, 20) / 10,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Product Attributes
            |--------------------------------------------------------------------------
            */
            if (! empty($data['attributes'])) {
                $valueIds = AttributeValue::whereIn(
                    'value',
                    $data['attributes']
                )->pluck('id');

                $product->attributeValues()->sync($valueIds);
            } else {
                $product->attributeValues()->detach();
            }

            /*
            |--------------------------------------------------------------------------
            | Product Variants
            |--------------------------------------------------------------------------
            */
            if (! empty($data['variants'])) {
                foreach ($data['variants'] as $variant) {
                    ProductVariant::updateOrCreate(
                        [
                            'sku' => $variant['sku'],
                        ],
                        [
                            'product_id' => $product->id,
                            'label' => $variant['label'],
                            'options' => $variant['options'],
                            'price' => $variant['price'],
                            'stock_quantity' => $variant['stock_quantity'],
                        ]
                    );
                }
            } else {
                /*
                |--------------------------------------------------------------------------
                | If product is simple, remove old variants if any existed.
                |--------------------------------------------------------------------------
                */
                $product->variants()->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Product Images
            |--------------------------------------------------------------------------
            */
            if ($product->images()->count() === 0) {
                foreach ($data['images'] as $imageIndex => $imageUrl) {
                    $imagePath = $this->downloadMedia(
                        $imageUrl,
                        "products/{$slug}-{$imageIndex}.jpg"
                    );

                    $product->images()->create([
                        'image' => $imagePath,
                        'sort_order' => $imageIndex + 1,
                    ]);
                }
            }
        }
    }

    /**
     * Generate a stable product SKU.
     */
    private function productSku(string $name): string
    {
        return 'PROD-' . strtoupper(
            Str::substr(
                Str::slug($name, ''),
                0,
                8
            )
        );
    }

    /**
     * Download product image to public storage.
     */
    private function downloadMedia(
        string $url,
        string $relativePath
    ): string {
        $storage = Storage::disk('public');

        $directory = dirname($relativePath);

        if ($directory !== '.') {
            $storage->makeDirectory($directory);
        }

        $fullPath = $storage->path($relativePath);

        if (! file_exists($fullPath)) {
            $contents = @file_get_contents($url);

            if ($contents !== false) {
                file_put_contents($fullPath, $contents);
            }
        }

        return $relativePath;
    }
}
