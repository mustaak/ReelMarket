<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private array $colors = [
        [41, 128, 185], [39, 174, 96], [211, 84, 0],
        [142, 68, 173], [192, 57, 43], [22, 160, 133],
    ];

    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'category' => 'Mobiles',
                'brand' => 'Sony',
                'price' => 4999,
                'sale_price' => 3999,
                'stock' => 45,
                'attributes' => ['Black', 'White'],
                'images' => [
                    'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'name' => 'Running Shoes',
                'category' => null,
                'brand' => 'Nike',
                'price' => 5499,
                'sale_price' => null,
                'stock' => 30,
                'attributes' => ['Red', 'Black', 'M', 'L', 'XL'],
                'images' => [
                    'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'name' => 'Smart LED TV 43 inch',
                'category' => 'Electronics',
                'brand' => 'Samsung',
                'price' => 32999,
                'sale_price' => 28999,
                'stock' => 12,
                'attributes' => [],
                'images' => [
                    'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1490933991293-4e1d37c4d1ce?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'name' => 'Cotton Casual T-Shirt',
                'category' => 'Men',
                'brand' => 'Puma',
                'price' => 899,
                'sale_price' => 649,
                'stock' => 100,
                'attributes' => ['Blue', 'White', 'S', 'M', 'L', 'Cotton'],
                'images' => [
                    'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'name' => 'Gaming Laptop 15.6"',
                'category' => 'Laptops',
                'brand' => 'Apple',
                'price' => 89999,
                'sale_price' => null,
                'stock' => 5,
                'attributes' => [],
                'images' => [
                    'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'name' => 'Yoga Mat',
                'category' => 'Fitness Equipment',
                'brand' => null,
                'price' => 1299,
                'sale_price' => 999,
                'stock' => 0,
                'attributes' => ['Green'],
                'images' => [
                    'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
        ];

        Storage::disk('public')->makeDirectory('products');

        foreach ($products as $index => $data) {
            $slug = Str::slug($data['name']);

            $product = Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'short_description' => "High quality {$data['name']}.",
                    'description' => "<p>This is a detailed description for {$data['name']}. Great quality and value for money.</p>",
                    'price' => $data['price'],
                    'sale_price' => $data['sale_price'],
                    'cost_price' => $data['price'] * 0.6,
                    'stock_quantity' => $data['stock'],
                    'status' => 'active',
                    'visibility' => 'visible',
                    'featured' => $index < 2,
                    'category_id' => $data['category'] ? Category::where('name', $data['category'])->value('id') : null,
                    'brand_id' => $data['brand'] ? Brand::where('name', $data['brand'])->value('id') : null,
                    'weight' => rand(1, 20) / 10,
                ]
            );

            if (! empty($data['attributes'])) {
                $valueIds = AttributeValue::whereIn('value', $data['attributes'])->pluck('id');
                $product->attributeValues()->sync($valueIds);
            }

            if ($product->images()->count() === 0) {
                foreach ($data['images'] as $imageIndex => $imageUrl) {
                    $imagePath = $this->downloadMedia($imageUrl, "products/{$slug}-{$imageIndex}.jpg");

                    $product->images()->create([
                        'image' => $imagePath,
                        'sort_order' => $imageIndex + 1,
                    ]);
                }
            }
        }
    }

    private function downloadMedia(string $url, string $relativePath): string
    {
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